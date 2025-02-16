Die funktion phore_glob() wird verwendet, um Dateien rekursiv zu durchsuchen. Die Funktion gibt ein Array von Dateipfaden zurück, die mit dem Muster übereinstimmen. Die Funktion wird verwendet, um Dateien zu finden, die in einem bestimmten Verzeichnisbaum vorhanden sind.

Paramter:

- $pattern (string|string[]): Ein oder mehrere Muster, das für die Suche verwendet wird.
  - Beispiel: '/*.md'
  - Beispiel: '/docs/*.md'
  - Beispiel: '/docs/**/*.md' -> Rekursiv in allen unterverzeichnisssen suchen
  - Beispiel: '/docs/**/index.(md|tx)' -> Rekursiv in allen unterverzeichnisssen nach index.md suchen

Die Muster unterstützen Reguläre Ausdrücke. Allerdings werden . und * ersetzt

- $exclude (string|string[]): Ein oder mehrere Muster, die von der Suche ausgeschlossen werden.
  - Beispiel: '**/node_modules/**'
  - Beispiel: 'node_modules'
  
Auch hier werden Reguläre Ausdrücke unterstützt.

Doppelte Dateien werden nur einmal zurückgegeben.


Funktonsweise:

der Pfad vor einem * wird als Bassiverzeichnis genutzt, in dem alle Dateien 
rekursiv gesucht werden. Es wird gegen das Muster geprüft, ob es mit dem Pfad übereinstimmt. Wenn ja, wird der Pfad zurückgegeben. Wenn nicht, wird der Pfad nicht zurückgegeben.


Erstelle die Funktion mit erklärung in php 8
