#!/usr/bin/env php
<?php 
/*
 * Copyright (C) 2015 Marcel Bollmann <bollmann@linguistics.rub.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */ ?>
<?php
require_once 'cli_status_bar.php';

$CORA_DIR = include 'cora_config_webdir.php';
require_once( $CORA_DIR . "/lib/cfg.php" );
require_once( $CORA_DIR . "/lib/connect/TagsetAccessor.php" );
$dbinfo = Cfg::get('dbinfo');
$dbo = new PDO('mysql:host='.$dbinfo['HOST']
              .';dbname='.$dbinfo['DBNAME']
              .';charset=utf8',
               $dbinfo['USER'], $dbinfo['PASSWORD']);

function exactly_one_of($list, $opts) {
  $count = 0;
  foreach($list as $k) {
    $count += array_key_exists($k, $opts);
  }
  return ($count == 1);
}

function exit_with_errors($errors) {
  $count = count($errors);
  if ($count === 0) return;
  foreach ($errors as $err) {
    echo("*** ERROR: {$err}\n");
  }
  if ($count > 1)
    exit("\nThere were {$count} errors.\n");
  else
    exit("\nThere was an error.\n");
}

$options = getopt("f:t:diux");
if (array_key_exists("h", $options)
    || !array_key_exists("f", $options)
    || !array_key_exists("t", $options)
    || !exactly_one_of(array("d", "i", "u"), $options)) {
?>

Updates a tagset with tags from a text file.

    Usage:
    <?php echo $argv[0]; ?> -f <list> -t <id> (-d | -i | -u) [-x]

    Reads all tags in the file <list> and updates the tagset with
    the ID <id> according to the chosen option:

    -d  Deletes all tags contained in <list>; tags that are still
        linked to are marked as needing revision, while other tags
        are hard-deleted.

    -i  Inserts all tags contained in <list>.

    -u  Updates the tagset to match the contents of <list>,
        deleting and inserting tags as required.

    Use option -x to actually perform these changes.

<?php
    exit;
}

// set from options
$file_contents = file_get_contents($options["f"]);
if(!$file_contents) {
  exit("ERROR: Couldn't open file for reading.\n");
}
$taglist = array_map('trim', explode("\n", $file_contents));
$execute = array_key_exists("x", $options);
if (array_key_exists("d", $options)) {
  $mode = 'delete';
}
else if (array_key_exists("i", $options)) {
  $mode = 'insert';
}
else if (array_key_exists("u", $options)) {
  $mode = 'update';
}

// instantiate TagsetAccessor
$tagset = new TagsetAccessor($dbo, $options["t"]);
if ($tagset->hasErrors()) exit_with_errors($tagset->getErrors());

echo("Tagset ID {$tagset->getID()}: {$tagset->getName()} "
     . "({$tagset->getClass()}, {$tagset->getSetType()})\n");
if ($tagset->getSetType() !== 'closed') {
  exit_with_errors(array("Tagset is not a closed-class tagset."));
}

// perform actions
$done = 0;
if ($mode === 'update') {
  $oldlist = $tagset->entries();
  $total = count($taglist) + count($oldlist);
  foreach ($taglist as $value) {
    if (isset($oldlist[$value])) {
      if ($oldlist[$value]['needs_revision'] == 1) {
        $tagset->setRevisionFlagForTag($value, 0);
      }
      unset($oldlist[$value]);
      $done += 2;
    }
    else {
      $tagset->addTag($value);
      $done++;
    }
    show_status($done, $total);
  }
  foreach ($oldlist as $value => $tag) {
    if ($tag['needs_revision'] == 0) {
      $tagset->deleteOrMarkTag($value);
    }
    $done++;
    show_status($done, $total);
  }
}
else {
  $total = count($taglist);
  foreach ($taglist as $tag) {
    if ($mode === 'delete')
      $tagset->deleteOrMarkTag($tag);
    else if ($mode === 'insert')
      $tagset->addTag($tag);
    $done++;
    show_status($done, $total);
  }
}

// summary
if ($tagset->hasErrors()) exit_with_errors($tagset->getErrors());
$status = array('new' => 0, 'delete' => 0, 'update' => 0, 'unchanged' => 0);
foreach ($tagset->entries() as $value => $tag) {
  if (!isset($tag['status']))
    $status['unchanged']++;
  else
    $status[$tag['status']]++;
}
echo("\n");
echo("      New: {$status['new']}\n");
echo("  Deleted: {$status['delete']}\n");
echo(" Modified: {$status['update']}\n");
echo("Unchanged: {$status['unchanged']}\n");

// execute?
if(!$execute) {
  echo("\nCall this script again with option -x to perform these changes.\n");
  exit;
}

$tagset->commitChanges();
if ($tagset->hasErrors()) exit_with_errors($tagset->getErrors());

echo("\nSuccessfully changed.\n");

?>
