<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 11.12.19
 * Time: 18:05
 */


function phore_validate() {


}



phore_validate($input, ":string");
phore_validate($input, ":int");
phore_validate($input, ":float");
phore_validate($input, ":ip");
phore_validate($input, ":ipv4");
phore_validate($input, ":ipv6");
phore_validate($input, ":hostname");
phore_validate($input, ":email");

phore_validate($input, ":[0..10].:string");

phore_validate($input, ":[admin_users,def,kfa?]");
phore_validate($input, "abc.:[cde,efg,kes].");


phore_validate($input, ["abc?", ":[]", ":int"]);

phore_validate($input, ":<InputType>")
