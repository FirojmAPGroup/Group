<?php

namespace App\Helpers;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

use Symfony\Component\HttpFoundation\StreamedResponse;


class XlsxHelper{

	public function init() {
		// Init this component
	}

	public function __construct(){}

	public static function objSpreadsheet(){ return new Spreadsheet(); }
	public static function objWriterXlsx($spreadsheet){ return new Xlsx($spreadsheet); }

	// All data start from 10th row, after the logo and title
	public static function startLine(){ return 3; }

	// Download
	public static function download($spreadsheet, $filename){
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$writer = self::objWriterXlsx($spreadsheet);
		$writer->setIncludeCharts(true);
		$writer->save('php://output');
		die();
	}

	// For Logo & Main Heading of XLSX
	public static function applyLogoAndTitle($spreadsheet, $title='', $description=''){
		// for logo

		// $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
		// $drawing->setName('Jullo Roots Logo');
		// $drawing->setDescription('Jullo Roots Logo');
		// $drawing->setPath(public_path().'/assets/images/random-avatar1.jpg'); // put your path and image here
		// $drawing->setCoordinates('A1');
		// $drawing->setOffsetX(15);
		// $drawing->setOffsetY(0);
		// //$drawing->setRotation(25);
		// $drawing->getShadow()->setVisible(true);
		// //$drawing->getShadow()->setDirection(45);
		// $drawing->setWorksheet($spreadsheet->getActiveSheet());

		// Set document properties
		if($title || $description){
			$spreadsheet->getActiveSheet();
			$spreadsheet->getProperties()
				->setCreator("Jullo Roots")
				->setTitle($title)
				->setSubject($title)
				->setDescription($description);
				//->setLastModifiedBy("Maarten Balliauw")->setKeywords("office 2007 openxml php")->setCategory("Test result file");

			$lines = 'A1:N2';
			$spreadsheet->setActiveSheetIndex(0)->setCellValue('A1', $title);
			$spreadsheet = self::applyOutlineBorder($spreadsheet, $lines);
			$spreadsheet = self::applyBold($spreadsheet, $lines);
			$spreadsheet = self::applyFontSize($spreadsheet, $lines, 13);
			$spreadsheet = self::applyMerge($spreadsheet, $lines);
			$spreadsheet = self::applyTextCenter($spreadsheet, $lines);
			$spreadsheet = self::applyAllBorder($spreadsheet, $lines);
		}
		return $spreadsheet;
	}

	// XLSX Styling Functions
	public static function setWidth($spreadsheet, $col, $width=15){
		$spreadsheet->getActiveSheet()->getColumnDimension($col)->setWidth($width);
		return $spreadsheet;
	}

	public static function applyBold($spreadsheet, $lines){
		$spreadsheet->getActiveSheet()->getStyle($lines)->getFont()->setBold(true);
		return $spreadsheet;
	}
	public static function applyFontSize($spreadsheet, $lines, $size=10){
		$spreadsheet->getActiveSheet()->getStyle($lines)->getFont()->setSize($size);
		return $spreadsheet;
	}
	public static function applyMerge($spreadsheet, $lines){
		$spreadsheet->getActiveSheet()->mergeCells($lines);
		return $spreadsheet;
	}
	public static function applyTextCenter($spreadsheet, $lines){
		$spreadsheet->getActiveSheet()->getStyle($lines)->getAlignment()->setHorizontal('center');
		$spreadsheet->getActiveSheet()->getStyle($lines)->getAlignment()->setVertical('center');
		return $spreadsheet;
	}
	public static function applyAllBorder($spreadsheet, $lines){
		#$spreadsheet->getActiveSheet()->getStyle($lines)->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		#$spreadsheet->getActiveSheet()->getStyle($lines)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		#$spreadsheet->getActiveSheet()->getStyle($lines)->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		#$spreadsheet->getActiveSheet()->getStyle($lines)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		$spreadsheet->getActiveSheet()->getStyle($lines)->applyFromArray([
			'borders' => [
				'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['argb' => '00000000']]
			]
		]);
		return $spreadsheet;
	}
	public static function applyOutlineBorder($spreadsheet, $lines){
		$spreadsheet->getActiveSheet()->getStyle($lines)->applyFromArray([
			'borders' => [
				'outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['argb' => '00000000']]
			]
		]);
		return $spreadsheet;
	}

	// Set Cell Value
	public static function setValue($spreadsheet, $lines, $value, $styles=[]){
		$value = str_replace(['<br />', '<br>'], PHP_EOL, $value);

		$spreadsheet->setActiveSheetIndex(0)->setCellValue($lines, $value);
		if(in_array('bold', $styles)) $spreadsheet = self::applyBold($spreadsheet, $lines);
		if(in_array('allborder', $styles)) $spreadsheet = self::applyAllBorder($spreadsheet, $lines);
		if(in_array('textcenter', $styles)) $spreadsheet = self::applyTextCenter($spreadsheet, $lines);
		//$spreadsheet = self::applyFontSize($spreadsheet, $lines, 13);
		return $spreadsheet;
	}

	// Render Bar And Line Chart
	public static function renderBarAndLineChart($spreadsheet, $args=[]){
		$worksheet = $spreadsheet->getActiveSheet();

		// Set the X-Axis Labels
		//     Datatype
		//     Cell reference for data
		//     Format Code
		//     Number of datapoints in series
		//     Data values
		//     Data Marker
					// new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$5', null, 4), // Q1 to Q4
		$xAxisTickValues = [
			new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, count($args['xAxis']), $args['xAxis']),
		];

		// Set the Labels for each data series we want to plot
		//     Datatype
		//     Cell reference for data
		//     Format Code
		//     Number of datapoints in series
		//     Data values
		//     Data Marker
					// new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$1', null, 1), // 2010
		$dataSeriesLabels = [];
		foreach($args['yAxis'] as $yAxis)
			$dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, [$yAxis['label']]);

		// Set the Data values for each data series we want to plot
		//     Datatype
		//     Cell reference for data
		//     Format Code
		//     Number of datapoints in series
		//     Data values
		//     Data Marker
					// new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$2:$B$5', null, 4),
		$dataSeriesValues = [];
		foreach($args['yAxis'] as $yAxis){
			$dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, null, null, count($yAxis['value']), $yAxis['value']);
		}

		// Build the dataseries
		$series = new DataSeries(
				DataSeries::TYPE_BARCHART, // plotType
				DataSeries::GROUPING_STANDARD, // plotGrouping
				range(0, count($dataSeriesValues) - 1), // plotOrder
				$dataSeriesLabels, // plotLabel
				$xAxisTickValues, // plotCategory
				$dataSeriesValues // plotValues
		);
		// Set additional dataseries parameters
		//     Make it a horizontal bar rather than a vertical column graph
		$series->setPlotDirection(DataSeries::DIRECTION_COL);

		// Create the chart
		$chart = new Chart(
				'chart1', // name
				new Title($args['xTitle']), // title
				new Legend(Legend::POSITION_BOTTOM, null, false), // Set the chart legend
				new PlotArea(null, [$series]), // Set the series in the plot area
				true, // plotVisibleOnly
				DataSeries::EMPTY_AS_GAP, // displayBlanksAs
				null, // xAxisLabel
				new Title($args['yTitle'])  // yAxisLabel
		);

		// Set the position where the chart should appear in the worksheet
		$chart->setTopLeftPosition($args['posTopLeft']);
		$chart->setBottomRightPosition($args['posBottomRight']);

		// Add the chart to the worksheet
		$worksheet->addChart($chart);

		return $spreadsheet;
	}

	// Read File In Chunk
	public static function readChunk($filePath, $start, $chunk=1000){
		$chunkFilter = new MyReadFilter();
		$chunkFilter->setRows($start, $chunk);

		$objReader = IOFactory::createReader(IOFactory::identify($filePath));
		$objReader->setReadFilter($chunkFilter);
		$spreadsheet = $objReader->load($filePath);

		$data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

		$return = [];
		for($i=$start; $i<($start+$chunk); $i++)
			if(isset($data[$i]))
				$return[$i] = $data[$i];
		return isset($return) ? $return : [];
	}
}

// Custom Class For Chunk Read Filter
class MyReadFilter implements IReadFilter {
	private $_stRow = 0;
	private $_enRow = 0;
	public function setRows($stRow, $chunkSize) {
		$this->_stRow = $stRow;
		$this->_enRow = $stRow + $chunkSize;
	}
	public function readCell($column, $row, $worksheetName = '') {
		// Read title row and rows 20 - 30 // ($row == 1 || ($row >= 20 && $row <= 30))
		if (($row >= $this->_stRow && $row < $this->_enRow)) {
			return true;
		}
		return false;
  }
}
