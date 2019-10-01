<?php


namespace Phore\Core\Helper;


class PhoreSimpleParallelRunner
{

    /**
     * Spin up n procs using pcntl_fork() and wait until they
     * exit. If the master process exists, these procs are removed
     *
     * @param callable $function
     * @param int $nprocs
     * @throws \Exception
     */
    public static function Run(callable $function, int $nprocs=1)
    {
        $pids= [];
        for ($i=0; $i<4; $i++) {
            $pid = pcntl_fork();
            if ($pid === -1)
                throw new \Exception("Cannot fork()");
            if ($pid) {
                // Parent
                $pids[] = $pid;
            } else {
                $function();
            }
        }
        while (count($pids) > 0) {
            $exitPid = pcntl_wait($status);
            $pids = array_diff($pids, [$exitPid]);
        }
    }


}
