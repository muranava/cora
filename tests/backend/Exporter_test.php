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
require_once"data/test_data.php";

require_once"{$GLOBALS['CORA_WEB_DIR']}/lib/exporter.php";

/** A mock DBInterface
 */
class Cora_Tests_DBInterface_Exporter_Mock {
    private $test_data;

    function __construct() {
        $this->test_data = get_Exporter_data();
    }

    public function getExpectedPOS() {
        return $this->test_data["expected_POS"];
    }
    public function getExpectedNorm() {
        return $this->test_data["expected_norm"];
    }
    public function getExpectedTagging_First()
    {
        return $this->test_data["expected_tagging_1"];
    }
    public function getExpectedTagging_Second()
    {
        return $this->test_data["expected_tagging_2"];
    }
    public function getExpectedTraining()
    {
        return $this->test_data["expected_training"];
    }

    public function getFilesForProject($projectid) {
        return array(array("id" => 42),
                     array("id" => 43));
    }

    public function getAllModerns($fileid) {
        $tokens = $this->getAllTokens($fileid);
        if(isset($tokens[2]))
            return $tokens[2];
        else
            return $tokens;
    }

    public function getAllTokens($fileid) {
        if($fileid == 42) {
            return $this->test_data["all_tokens"];
        } else if ($fileid == 43) {
            return $this->test_data["all_tok_43"];
        } else {
            return array();
        }
    }
}

class Cora_Tests_Exporter_test extends PHPUnit_Framework_TestCase {
    protected $dbi;
    protected $exp;

    protected function setUp() {
        $this->dbi = new Cora_Tests_DBInterface_Exporter_Mock();
        $this->exp = new Exporter($this->dbi);
    }

    public function testExportPOS() {
        $stream = fopen("php://memory", 'r+');
        $result = $this->exp->export(42, ExportType::Tagging, array(), $stream);
        rewind($stream);

        $this->assertEquals($this->dbi->getExpectedPOS(),
                            stream_get_contents($stream));
        fclose($stream);
    }

    public function testExportNorm() {
        $stream = fopen("php://memory", 'r+');
        $result = $this->exp->export(42, ExportType::Normalization, array(), $stream);
        rewind($stream);

        $this->assertEquals($this->dbi->getExpectedNorm(),
                            stream_get_contents($stream));
        fclose($stream);
    }

    public function testExportForTagging_First() {
        $classes = array("pos", "norm");
        $stream  = fopen("php://memory", 'r+');
        $moderns = $this->exp->exportForTagging(42, $stream, $classes, true);
        rewind($stream);

        $this->assertEquals($this->dbi->getExpectedTagging_First(),
                            stream_get_contents($stream));
        $this->assertEquals($this->dbi->getAllTokens(42)[2],
                            $moderns);
        fclose($stream);
    }

    public function testExportForTagging_Second() {
        $classes = array("norm");
        $stream  = fopen("php://memory", 'r+');
        $moderns = $this->exp->exportForTagging(42, $stream, $classes, false);
        rewind($stream);

        $this->assertEquals($this->dbi->getExpectedTagging_Second(),
                            stream_get_contents($stream));
        $this->assertEquals($this->dbi->getAllTokens(42)[2],
                            $moderns);
        fclose($stream);
    }

    public function testExportForTraining() {
        $classes = array("pos", "norm");
        $stream  = fopen("php://memory", 'r+');
        $this->exp->exportForTraining(99, $stream, $classes, true);
        rewind($stream);

        $this->assertEquals($this->dbi->getExpectedTraining(),
                            stream_get_contents($stream));
        fclose($stream);
    }

}
?>
