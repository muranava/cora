<?php

 /** @file DocumentAccessor.php
  * Functions related to loading and saving of documents.
  *
  * @author Marcel Bollmann
  * @date December 2013
  */

class DocumentAccessViolation extends Exception {
}

/** Handles document access that is potentially used by several
 * operations (e.g., reading and writing).
 * 
 * More specialized classes will extend this class.
 */
class DocumentAccessor {
  protected $dbi; /**< DBInterface object to use for queries */
  protected $dbo; /**< PDO object to use for own queries */
  protected $fileid; /**< ID of the associated file */

  protected $warnings = array();

  private $stmt_isValidModID = null;
  private $stmt_getSelectedAnnotations = null;
  private $stmt_getCoraComment = null;

  /** Construct a new DocumentAccessor.
   *
   * @param DBInterface $parent A DBInterface object to use for queries
   * @param PDO $dbo A PDO database object passed from DBInterface
   * @param string $fileid ID of the file to be accessed
   */
  function __construct($parent, $dbo, $fileid) {
    $this->dbi = $parent;
    $this->dbo = $dbo;
    $this->fileid = $fileid;

    $this->prepare_isValidModID();
    $this->prepare_getSelectedAnnotations();
    $this->prepare_getCoraComment();
  }

  protected function warn($message) {
    $this->warnings[] = $message;
  }

  public function getWarnings() {
    return $this->warnings;
  }

  /**********************************************
   ********* SQL Statement Preparations *********
   **********************************************/

  private function prepare_isValidModID() {
    $stmt = "SELECT modern.id FROM modern "
      . " LEFT JOIN token     ON   modern.tok_id=token.id "
      . " LEFT JOIN text      ON   token.text_id=text.id "
      . "     WHERE text.id={$this->fileid} "
      . "           AND modern.id=?";
    $this->stmt_isValidModID = $this->dbo->prepare($stmt);
  }

  private function prepare_getSelectedAnnotations() {
    $stmt = "SELECT ts.id, ts.tag_id, ts.source, tag.value, tagset.class "
      . "      FROM tag_suggestion ts "
      . " LEFT JOIN tag     ON tag.id=ts.tag_id "
      . " LEFT JOIN tagset  ON tagset.id=tag.tagset_id "
      . "     WHERE ts.selected=1 AND ts.mod_id=?";
    $this->stmt_getSelectedAnnotations = $this->dbo->prepare($stmt);
  }

  private function prepare_getCoraComment() {
    $stmt = "SELECT modern.id, comment.id AS comment_id, token.id AS token_id"
      . "      FROM modern"
      . " LEFT JOIN token   ON modern.tok_id=token.id"
      . " LEFT JOIN comment ON comment.tok_id=token.id"
      . "                  AND comment.subtok_id=modern.id"
      . "                  AND comment.comment_type='C'"
      . "     WHERE modern.id=?";
    $this->stmt_getCoraComment = $this->dbo->prepare($stmt);
  }

  /**********************************************/

  /** Test whether a given mod ID belongs to the associated file.
   *
   * @param string $modid ID of the mod to be tested
   */
  public function isValidModID($modid) {
    $this->stmt_isValidModID->execute(array($modid));
    return $this->stmt_isValidModID->fetch();
  }

  /** Retrieve selected annotations for a given mod ID.
   *
   * @param string $modid A mod ID
   */
  public function getSelectedAnnotations($modid) {
    $this->stmt_getSelectedAnnotations->execute(array($modid));
    return $this->stmt_getSelectedAnnotations->fetchAll(PDO::FETCH_ASSOC);
  }

  /** Retrieve CorA-internal comment for a given mod ID.
   *
   * @param string $modid A mod ID
   */
  public function getCoraComment($modid) {
    $this->stmt_getCoraComment->execute(array($modid));
    return $this->stmt_getCoraComment->fetch(PDO::FETCH_ASSOC);
  }

  /** Retrieve selected annotations for a given mod ID and index them
   * by class attribute of the tagset.
   *
   * @param string $modid A mod ID
   */
  public function getSelectedAnnotationsByClass($modid) {
    $annotations = array();
    $selected = $this->getSelectedAnnotations($modid);
    foreach($selected as $row) {
      $annotations[$row['class']] = $row;
    }
    return $annotations;
  }


}