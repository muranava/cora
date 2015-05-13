<?php
/** @file gui.php
 * Display the graphical user interface.
 */
?>
<!DOCTYPE html>
<html lang="en" xml:lang="en">
  <head>
    <meta charset="utf-8" />
    <title><?php echo TITLE . " (" . LONGTITLE . ") " . VERSION; ?></title>
    <meta name="description" content="<?php echo DESCRIPTION; ?>" />
    <meta name="keywords" content="<?php echo KEYWORDS; ?>" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,700,400,600' rel='stylesheet' type='text/css'>

    <?php
    /**************** Cascading Style Sheets ****************/
      $master_css = file_exists(dirname(__FILE__)."/gui/css/master.min.css")
                    ? "gui/css/master.min.css"
                    : "gui/css/master.css";
      embedCSS($master_css, "all", true);
      embedCSS("gui/css/open-iconic.min.css", "all", true);
      if($_SESSION['admin'])
          embedCSS("gui/css/datepicker.css", "all", true);
      //// this is currently not working:
      // embedCSS("gui/css/print.css", "print", false);

    /********************** JavaScript **********************/
    ?>
    <script type="text/javascript">
        var cora = {strings: {}};
        var default_tab = "<?php echo $menu->getDefaultItem(); ?>";
        <?php
          if($_SESSION['loggedIn']) {
              $svars = array('noPageLines' => false,
                             'contextLines' => false,
                             'editTableDragHistory' => true,
                             'hiddenColumns' => true,
                             'textPreview' => true,
                             'admin' => true,
                             'currentFileId' => true,
                             'currentName' => true,
                             'showInputErrors' => false,
                             'locale' => true
                             );
              embedTagsets($tagsets_all);
          }
          else {
              $svars = array('locale' => true);
          }
          embedSessionVars($svars);
        ?>
    </script>
    <?php
      embedJS("gui/js/mootools-core-1.4.5-full-compat-yc.js");
      embedJS("gui/js/mootools-more-1.4.0.1-min.js");

      if($_SESSION['loggedIn']) {
          include("project_specific_hacks.php");

          if(file_exists(dirname(__FILE__) . "/gui/js/mbox.min.js")) {
              embedJS("gui/js/mbox.min.js", true);
          } else {
              embedJS("gui/js/mbox/mBox.Core.js", true);
              embedJS("gui/js/mbox/mBox.Modal.js", true);
              embedJS("gui/js/mbox/mBox.Notice.js", true);
              embedJS("gui/js/mbox/mBox.Modal.Confirm.js", true);
              embedJS("gui/js/mbox/mBox.Tooltip.js", true);
              embedJS("gui/js/mbox/mForm.Core.js", true);
              embedJS("gui/js/mbox/mForm.Submit.js", true);
              embedJS("gui/js/mbox/mForm.Element.js", true);
              embedJS("gui/js/mbox/mForm.Element.Select.js", true);
          }

          if(file_exists(dirname(__FILE__) . "/gui/js/master.min.js")) {
              embedJS("gui/js/master.min.js", true);
          } else {
              embedJS("gui/js/request/CoraRequestErrors.js", true);
              embedJS("gui/js/request/CoraRequest.js", true);
              embedJS("gui/js/settings.js", true);
              embedJS("gui/js/MultiSelect.js", true);
              embedJS("gui/js/baseBox.js", true);
              embedJS("gui/js/ProgressBar.js", true);
              // embedJS("gui/js/dragtable_hack.js", true);
              embedJS("gui/js/iFrameFormRequest.js", true);
              embedJS("gui/js/Meio.Autocomplete.js", true);
              embedJS("gui/js/FlexRowList.js", true);
              embedJS("gui/js/gui.js", true);
              embedJS("gui/js/tagsets.js", true);
              embedJS("gui/js/tagsets/Tagset.js", true);
              embedJS("gui/js/tagsets/SplitClassTagset.js", true);
              embedJS("gui/js/tagsets/POS.js", true);
              embedJS("gui/js/tagsets/Norm.js", true);
              embedJS("gui/js/tagsets/NormBroad.js", true);
              embedJS("gui/js/tagsets/NormType.js", true);
              embedJS("gui/js/tagsets/LemmaAutocomplete.js", true);
              embedJS("gui/js/tagsets/Lemma.js", true);
              embedJS("gui/js/tagsets/LemmaSugg.js", true);
              embedJS("gui/js/tagsets/LemmaPOS.js", true);
              embedJS("gui/js/tagsets/Boundary.js", true);
              embedJS("gui/js/tagsets/Comment.js", true);
              embedJS("gui/js/tagsets/TagsetFactory.js", true);
              embedJS("gui/js/file.js", true);
              embedJS("gui/js/edit/DataSource.js", true);
              embedJS("gui/js/edit/DataTableNavigation.js", true);
              embedJS("gui/js/edit/DataTableProgressBar.js", true);
              embedJS("gui/js/edit/DataTableDropdownMenu.js", true);
              embedJS("gui/js/edit/DataTable.js", true);
              embedJS("gui/js/edit/FlagHandler.js", true);
              embedJS("gui/js/edit/LineJumper.js", true);
              embedJS("gui/js/edit/TokenSearcher.js", true);
              embedJS("gui/js/edit/SearchResults.js", true);
              embedJS("gui/js/edit/HorizontalTextPreview.js", true);
              embedJS("gui/js/edit/PageModel.js", true);
              embedJS("gui/js/edit/EditorModelUndo.js", true);
              embedJS("gui/js/edit/EditorModel.js", true);
              embedJS("gui/js/edit.js", true);
          }

          if($_SESSION['admin']) {
              if(file_exists(dirname(__FILE__) . "/gui/js/admin.min.js"))
                  embedJS("gui/js/admin.min.js", true);
              else {
                  embedJS("gui/js/datepicker.js", true);
                  embedJS("gui/js/admin.js", true);
              }
          }
      } else {  // not logged in
echo <<<NOTLOGGEDIN
    <script type="text/javascript">
      var gui = {changeTab: function() {}};

      window.addEvent('domready', function() {
          $('loginTabButton').set('active', 'true');

          var uri = new URI();
          if(uri.parsed && uri.parsed.query) {
              var fid = uri.parsed.query.parseQueryString()["f"];
              var form = document.getElement('#loginDiv form');
              if(fid && form) {
                  form.set('action', form.get('action') + "?f=" + fid);
              } else {
                  history.replaceState({}, "", "./");
              }
          }
      });
    </script>
NOTLOGGEDIN;
      }
    ?>
  </head>
  <?php flush(); ?>
  <body>
    <div id="overlay"></div>
    <div id="spin-overlay"></div>

    <!-- header -->
      <div id="header" class="no-print">
        <div id="titlebar">
          <span class="cora-title"><?php echo TITLE; ?></span>
          <span class="cora-version"><?php echo VERSION; ?></span>
          <span id="currentfile"></span>
        </div>
        <?php include( "gui/menu.php" ); ?>
      </div>

    <!-- main content -->
    <div id="main" class="no-print">
      <?php foreach ($menu->getItems() as $item) {
                include( $menu->getItemFile($item) );
	    }
      ?>
      <div id="footer">&copy; 2012&mdash;2015 Marcel Bollmann, Florian Petran, Sprachwissenschaftliches Institut, Ruhr-Universität Bochum</div>
    </div>

    <!-- templates -->
    <?php if($_SESSION['loggedIn']): ?>
    <div class="templateHolder">
      <div id="genericTextMsgPopup">
        <p></p>
        <p><textarea cols="80" rows="10" readonly="readonly"></textarea></p>
      </div>
      <div id="confirmLoginPopup">
        <p>Bitte geben Sie Ihre Zugangsdaten erneut ein, um fortzufahren.</p>
        <form class="loginForm">
          <label for="lipu_un">Benutzername:</label>
          <p class="text">
            <input type="text" name="lipu_un" id="lipu_un" disabled="disabled" value="<?php echo $_SESSION['user']; ?>" />
          </p>
          <label for="lipu_pw">Passwort:</label>
          <p class="text">
            <input type="password" name="lipu_pw" id="lipu_pw" value="" />
          </p>
        </form>
      </div>
      <?php include("news.php"); ?>
    </div>
    <?php endif; ?>
  </body>
</html>
