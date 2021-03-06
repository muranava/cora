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
/** @file file.php
 * The document selection page.
 */
?>

<div id="fileDiv" class="content">
  <div class="panel">
    <div class="btn-toolbar">
      <span class="btn-toolbar-entry" id="fileViewRefresh" title="<?=$lh("FileTab.topButton.refresh"); ?>" data-trans-title-id="FileTab.topButton.refresh">
        <span class="oi" data-glyph="reload" aria-hidden="true"></span>
      </span>
      <span class="btn-toolbar-entry" id="fileViewCollapseAll" title="<?=$lh("FileTab.topButton.collapseProjectGroups"); ?>" data-trans-title-id="FileTab.topButton.collapseProjectGroups">
        <span class="oi" data-glyph="collapse-up" aria-hidden="true"></span>
      </span>
      <span class="btn-toolbar-entry" id="fileViewExpandAll" title="<?=$lh("FileTab.topButton.expandProjectGroups"); ?>" data-trans-title-id="FileTab.topButton.expandProjectGroups">
        <span class="oi" data-glyph="collapse-down" aria-hidden="true"></span>
      </span>
      <span class="btn-toolbar-entry start-disabled" id="importNewTransLink">
        <span class="oi" data-glyph="data-transfer-upload" aria-hidden="true"></span>
        <span data-trans-id="FileTab.topButton.importText"><?=$lh("FileTab.topButton.importText"); ?></span>
      </span>
      <span class="btn-toolbar-entry start-disabled" id="importNewXMLLink">
        <span class="oi" data-glyph="data-transfer-upload" aria-hidden="true"></span>
        <span data-trans-id="FileTab.topButton.importXml"><?=$lh("FileTab.topButton.importXml"); ?></span>
      </span>
    </div>

    <div id="files"></div>
  </div>

  <!-- templates -->
  <div class="templateHolder">
    <span id="transImportSpinner_title" data-trans-id="FileTab.Forms.import.importingData"><?=$lh("FileTab.Forms.import.importingData"); ?></span>
    <div id="transImportSpinner">
      <div id="transImportStatusContainer">
        <table>
          <tr id="tIS_upload">
            <td class="proc proc-running"><span class="oi oi-proc-success oi-shadow" data-glyph="check" aria-hidden="true"></span>
              <span class="oi oi-proc-error oi-shadow" data-glyph="x" aria-hidden="true"></span></td>
            <td data-trans-id="FileTab.importDialog.transferringFile"><?=$lh("FileTab.importDialog.transferringFile"); ?></td>
          </tr>
          <tr id="tIS_check">
            <td class="proc"><span class="oi oi-proc-success oi-shadow" data-glyph="check" aria-hidden="true"></span>
              <span class="oi oi-proc-error oi-shadow" data-glyph="x" aria-hidden="true"></span></td>
            <td data-trans-id="FileTab.importDialog.checkingValidity"><?=$lh("FileTab.importDialog.checkingValidity"); ?></td>
          </tr>
          <tr id="tIS_convert">
            <td class="proc"><span class="oi oi-proc-success oi-shadow" data-glyph="check" aria-hidden="true"></span>
              <span class="oi oi-proc-error oi-shadow" data-glyph="x" aria-hidden="true"></span></td>
            <td data-trans-id="FileTab.importDialog.convertToXML"><?=$lh("FileTab.importDialog.convertToXML"); ?></td>
          </tr>
          <tr id="tIS_import">
            <td class="proc"><span class="oi oi-proc-success oi-shadow" data-glyph="check" aria-hidden="true"></span>
              <span class="oi oi-proc-error oi-shadow" data-glyph="x" aria-hidden="true"></span></td>
            <td data-trans-id="FileTab.importDialog.import"><?=$lh("FileTab.importDialog.import"); ?></td>
          </tr>
        </table>
        <div id="tIS_progress"></div>
      </div>
    </div>

    <div class="filegroup clappable clapp-modern" id="fileGroup">
      <h4 class="clapp"><span class="projectname"></span></h4>
        <div>
          <table class="fileList table-modern">
          <thead>
            <tr class="fileTableHeadLine">
              <th class="ftr-sigle" data-trans-id="FileTab.columnLabels.siglum"><?=$lh("FileTab.columnLabels.siglum"); ?></th>
              <th class="ftr-filename" data-trans-id="FileTab.columnLabels.fileName"><?=$lh("FileTab.columnLabels.fileName"); ?></th>
<!--              <th colspan="2" class="ftr-changed">Letzte Änderung am/von</th>
              <th colspan="2" class="ftr-created">Erstellt am/von</th>
-->
              <th class="ftr-changed-at" data-trans-id="FileTab.columnLabels.lastEdit"><?=$lh("FileTab.columnLabels.lastEdit"); ?></th>
              <th class="ftr-changed-by" data-trans-id="FileTab.columnLabels.by"><?=$lh("FileTab.columnLabels.by"); ?></th>
              <th class="ftr-created-at" data-trans-id="FileTab.columnLabels.created"><?=$lh("FileTab.columnLabels.created"); ?></th>
              <th class="ftr-created-by" data-trans-id="FileTab.columnLabels.by"><?=$lh("FileTab.columnLabels.by"); ?></th>
              <th class="ftr-id start-hidden admin-only" data-trans-id="FileTab.columnLabels.id"><?=$lh("FileTab.columnLabels.id"); ?></th>
              <th class="ftr-options table-th-nosort"></th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>

    <table>
      <tr id="fileTableRow">
        <td class="ftr-sigle"><a class="filenameOpenLink"></a></td>
        <td class="ftr-filename filename"><a class="filenameOpenLink"></a></td>
        <td class="ftr-changed-at"></td>
        <td class="ftr-changed-by"></td>
        <td class="ftr-created-at"></td>
        <td class="ftr-created-by"></td>
        <td class="ftr-id start-hidden admin-only"></td>
        <td class="ftr-options">
          <a class="deleteFileLink deletion-link start-hidden">
            <span class="oi oi-shadow oi-adjust" data-glyph="delete" title="<?=$lh("FileTab.columnLabels.deleteFile"); ?>" data-trans-title-id="FileTab.columnLabels.deleteFile" aria-hidden="true"></span>
          </a>
          <a class="exportFileLink">
            <span class="oi oi-shadow oi-adjust" data-glyph="data-transfer-download" title="<?=$lh("FileTab.columnLabels.exportFileTitle"); ?>" data-trans-title-id="FileTab.columnLabels.exportFileTitle" aria-hidden="true"></span>
            <span data-trans-id="FileTab.columnLabels.export"><?=$lh("FileTab.columnLabels.export"); ?></span>
          </a>
          <a class="editTagsetAssocLink start-hidden admin-only">
            <span class="oi oi-shadow oi-adjust" data-glyph="link-intact" title="<?=$lh("FileTab.columnLabels.editAssociatedTagsets"); ?>" data-trans-title-id="FileTab.columnLabels.editAssociatedTagsets" aria-hidden="true"></span>
            <span data-trans-id="FileTab.columnLabels.tagsets"><?=$lh("FileTab.columnLabels.tagsets"); ?>
            </a>
          <a class="closeFileLink start-hidden">
            <span class="oi oi-shadow" data-glyph="x" title="<?=$lh("FileTab.columnLabels.closeFileTitle"); ?>" data-trans-title-id="FileTab.columnLabels.closeFileTitle" aria-hidden="true"></span>
            <span data-trans-id="Action.close"><?=$lh("Action.close"); ?></span>
          </a>
        </td>
      </tr>
    </table>

    <span id="fileExportPopup_title" data-trans-id="FileTab.Forms.exportForm.exportFile"><?=$lh("FileTab.Forms.exportForm.exportFile"); ?></span>
    <div id="fileExportPopup" class="limitedWidth">
      <p data-trans-id="FileTab.Forms.exportForm.exportPrompt"><?=$lh("FileTab.Forms.exportForm.exportPrompt"); ?></span></p>
      <p class="file-export-format-selector">
        <input type="radio" name="file-export-format" value="<?php echo ExportType::CoraXML ?>" id="fef-coraxml" checked="checked" />
        <label for="fef-coraxml" data-trans-id="FileTab.Forms.exportForm.coraXmlFormat"><?=$lh("FileTab.Forms.exportForm.coraXmlFormat"); ?></label><br />
        <input type="radio" name="file-export-format" value="<?php echo ExportType::CustomCSV ?>" id="fef-customcsv" />
        <label for="fef-customcsv" data-trans-id="FileTab.Forms.exportForm.columnCsvFormat"><?=$lh("FileTab.Forms.exportForm.columnCsvFormat"); ?></label><br />
        <?php if($_SESSION["admin"]): ?>
          <input type="radio" name="file-export-format" value="<?php echo ExportType::Normalization ?>" id="fef-norm" />
          <label for="fef-norm" data-trans-id="FileTab.Forms.exportForm.columnNormFormat"><?=$lh("FileTab.Forms.exportForm.columnNormFormat"); ?></label><br />
          <input type="radio" name="file-export-format" value="<?php echo ExportType::Transcription ?>" id="fef-trans" disabled="disabled"/>
          <label for="fef-trans" data-trans-id="FileTab.Forms.exportForm.transFormat"><?=$lh("FileTab.Forms.exportForm.transFormat"); ?></label><br />
        <?php endif; ?>
      </p>
      <p class="for-fileexport for-<?php echo ExportType::CoraXML ?>" data-trans-id="FileTab.Forms.exportForm.exportInfoCora">
        <?=$lh("FileTab.Forms.exportForm.exportInfoCora"); ?>
      </p>
      <span class="start-hidden for-fileexport for-<?php echo ExportType::CustomCSV ?>">
        <p data-trans-id="FileTab.Forms.exportForm.exportInfoCsv">
          <?=$lh("FileTab.Forms.exportForm.exportInfoCsv"); ?>
        </p>
        <div class="export_CustomCSV_MS"></div>
      </span>
    </div>

    <span id="fileImportXMLForm_title" data-trans-id="FileTab.Forms.import.importXmlFile"><?=$lh("FileTab.Forms.import.importXmlFile"); ?></span>
    <div id="fileImportXMLForm" class="limitedWidth">
      <form action="request.php" id="newFileImportForm" method="post" accept-charset="utf-8" enctype="multipart/form-data">
      <p class="error_text error_text_import" data-trans-id="FileTab.Forms.import.chooseFile"><?=$lh("FileTab.Forms.import.chooseFile"); ?></p>
      <p>
        <label for="xmlFile" class="ra"><span data-trans-id="FileTab.Forms.import.file"><?=$lh("FileTab.Forms.import.file"); ?></span> </label>
        <input type="file" name="xmlFile" data-required="" />
      </p>
      <p>
        <label for="project" class="ra"><span data-trans-id="FileTab.Forms.import.project"><?=$lh("FileTab.Forms.import.project"); ?></span> </label>
        <select name="project" size="1"></select>
      </p>
      <p data-trans-id="FileTab.Forms.import.importInfo"><?=$lh("FileTab.Forms.import.importInfo"); ?></p>
      <p>
        <label for="xmlName" class="ra"><span data-trans-id="FileTab.Forms.import.docName"><?=$lh("FileTab.Forms.import.docName"); ?></span> </label>
        <input type="text" name="xmlName" placeholder="<?=$lh("FileTab.Forms.import.docNamePlaceholder"); ?>" data-trans-placeholder-id="FileTab.Forms.import.docNamePlaceholder" size="30" />
      </p>
      <p>
        <label for="sigle" class="ra"><span data-trans-id="FileTab.Forms.import.siglum"><?=$lh("FileTab.Forms.import.siglum"); ?></span> </label>
        <input type="text" name="sigle" placeholder="<?=$lh("FileTab.Forms.import.siglumPlaceholder"); ?>" data-trans-placeholder-id="FileTab.Forms.import.siglumPlaceholder" size="30" />
      </p>
      <div class="fileImportTagsetLinks" <?php if(!$_SESSION['admin']) {echo 'style="display:none;"';} ?>>
        <p style="padding-top: 15px;" data-trans-id="FileTab.Forms.import.associatedTagsets"><?=$lh("FileTab.Forms.import.associatedTagsets"); ?></p>
        <div class="import_LinkTagsets_MS"></div>
      </div>
      <p>
        <input type="hidden" name="action" value="importXMLFile" />
        <input type="hidden" name="via" value="iframe" />
      </p>
      <p style="text-align:right;">
        <input type="submit" value="<?=$lh("FileTab.Forms.import.importBtn"); ?>" data-trans-value-id="FileTab.Forms.import.importBtn" />
      </p>
      </form>
    </div>

    <span id="fileImportTransForm_title" data-trans-id="FileTab.Forms.import.importTextFile"><?=$lh("FileTab.Forms.import.importTextFile"); ?></span>
    <div id="fileImportTransForm" class="limitedWidth">
      <form action="request.php" id="newFileImportTransForm" method="post" accept-charset="utf-8" enctype="multipart/form-data">
      <p class="error_text error_text_import" data-trans-id="FileTab.Forms.import.chooseFile"><?=$lh("FileTab.Forms.import.chooseFile"); ?></p>
      <p>
        <label for="transFile" class="ra"><span data-trans-id="FileTab.Forms.import.file"><?=$lh("FileTab.Forms.import.file"); ?></span> </label>
        <input type="file" name="transFile" data-required="" />
      </p>
      <p>
        <label for="fileEnc" class="ra"><span data-trans-id="FileTab.Forms.import.encoding"><?=$lh("FileTab.Forms.import.encoding"); ?></span> </label>
        <select name="fileEnc" size="1">
          <option value="utf-8">UTF-8 (Unicode)</option>
          <option value="iso-8859-1">ISO-8859-1 (Latin 1)</option>
          <option value="IBM850">MS-DOS (IBM-850)</option>
        </select>
      </p>
      <p>
        <label for="project" class="ra"><span data-trans-id="FileTab.Forms.import.project"><?=$lh("FileTab.Forms.import.project"); ?></span> </label>
        <select name="project" size="1"></select>
      </p>
      <p class="error_text error_text_cmdimport" data-trans-id="FileTab.Forms.import.noImportScriptInfo"><?=$lh("FileTab.Forms.import.noImportScriptInfo"); ?></p>
      <p>
        <label for="transName" class="ra"><span data-trans-id="FileTab.Forms.import.docName"><?=$lh("FileTab.Forms.import.docName"); ?></span> </label>
        <input type="text" name="transName" placeholder="<?=$lh("FileTab.Forms.import.docNamePlaceholder"); ?>" data-trans-placeholder-id="FileTab.Forms.import.docNamePlaceholder" size="30" data-required="" />
      </p>
      <p>
        <label for="sigle" class="ra"><span data-trans-id="FileTab.Forms.import.siglum"><?=$lh("FileTab.Forms.import.siglum"); ?></span> </label>
        <input type="text" name="sigle" placeholder="<?=$lh("FileTab.Forms.import.siglumPlaceholder"); ?>" data-trans-placeholder-id="FileTab.Forms.import.siglumPlaceholder" size="30" />
      </p>
      <div class="fileImportTagsetLinks" <?php if(!$_SESSION['admin']) {echo 'style="display:none;"';} ?>>
        <p style="padding-top: 15px;" data-trans-id="FileTab.Forms.import.associatedTagsets"><?=$lh("FileTab.Forms.import.associatedTagsets"); ?></p>
        <div class="import_LinkTagsets_MS"></div>
      </div>
      <p>
        <input type="hidden" name="action" value="importTransFile" />
        <input type="hidden" name="via" value="iframe" />
      </p>
      <p style="text-align:right;">
        <input type="submit" value="<?=$lh("FileTab.Forms.import.importBtn"); ?>" data-trans-value-id="FileTab.Forms.import.importBtn" />
      </p>
      </form>
    </div>

    <div id="tagsetAssociationTable" class="limitedWidth">
      <table class="tagset-list">
        <thead>
        <tr><th></th><th class="numeric">ID</th><th>Name</th><th>Class</th><th>Set</th></tr>
        </thead>
        <tbody>
          <?php foreach($tagsets_all as $set): ?>
          <tr>
            <td class="check"><input type="checkbox" name="linktagsets[]" value="<?php echo $set['shortname']; ?>" /></td>
            <td class="numeric"><?php echo $set['shortname']; ?></td>
            <td><?php echo $set['longname']; ?></td>
            <td><?php echo $set['class']; ?></td>
            <td><?php echo $set['set_type']; ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div id="noProjectGroups">
      <h4 data-trans-id="FileTab.noProjectGroups"><?=$lh("FileTab.noProjectGroups"); ?></h4>
      <p data-trans-id="FileTab.noProjectGroupsInfo"><?=$lh("FileTab.noProjectGroupsInfo"); ?></p>
    </div>

    <table>
      <tr id="noProjectFiles">
        <td colspan="7" data-trans-id="FileTab.noFilesInProject"><?=$lh("FileTab.noFilesInProject"); ?></td>
      </tr>
    </table>
  </div>
</div>
