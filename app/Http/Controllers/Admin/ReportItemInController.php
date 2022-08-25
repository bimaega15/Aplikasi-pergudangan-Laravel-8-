<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\IncomingGoods;

use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Gate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportItemInController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $value = $request->input('value');
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');

            $incomingGoods = IncomingGoods::joinAllTable([], null, $value, $from_date, $to_date);
            return Datatables::of($incomingGoods)
                ->addIndexColumn()
                ->addColumn('picture_item', function ($incomingGoods) {
                    $pictureItem = $incomingGoods->picture_item != null ? public_path() . '/image/item/' . $incomingGoods->picture_item : false;
                    if (file_exists($pictureItem)) {
                        $pictureItemFix = asset('image/item/' . $incomingGoods->picture_item);
                    } else {
                        $pictureItemFix = asset('image/item/default.png');
                    }
                    $output = '
                    <a data-gallery="photoviewer" data-title="' . $incomingGoods->name_item . '" href="' . $pictureItemFix . '" alt="gambar-' . $incomingGoods->id . '" data-group="a">
                        <img src="' . $pictureItemFix . '" class="w-100 img-thumbnail"></img>
                    </a>
                    ';
                    return $output;
                })

                ->rawColumns(['picture_item'])
                ->make();
        }
        return view('admin.reportItemIn.index');
    }

    public function export(Request $request)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        $from_date = null;
        $to_date = null;
        $value = null;
        $judulLaporan = '';
        if (isset($_GET['from_date'])) {
            $from_date = $request->input('from_date');
        }
        if (isset($_GET['to_date'])) {
            $to_date = $request->input('to_date');
        }
        if ($from_date != null && $to_date != null) {
            $judulLaporan = 'Laporan_Transaksi_Barang_Masuk_Dari_Tanggal_' . $from_date . '_Sampai_Tanggal_' . $to_date;
        }
        if (isset($_GET['filter'])) {
            $value = urldecode($request->input('filter'));
            switch ($value) {
                case '-1 days':
                    $judulLaporan = 'Laporan_1_Hari_Terakhir_Transaksi_Barang_Masuk';
                    break;
                case '-7 days':
                    $judulLaporan = 'Laporan_Seminggu_Terakhir_Transaksi_Barang_Masuk';
                    break;
                case '-1 month':
                    $judulLaporan = 'Laporan_Sebulan_Terakhir_Transaksi_Barang_Masuk';
                    break;
                case 'all':
                    $judulLaporan = 'Laporan_Semua_Transaksi_Barang_Masuk';
                    break;
            }
        }
        $incomingGoods = IncomingGoods::joinAllTable([], null, $value, $from_date, $to_date);

        $spreadsheet = new Spreadsheet;
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('C1', 'No.')
            ->setCellValue('D1', 'Lokasi')
            ->setCellValue('E1', 'Kode barang')
            ->setCellValue('F1', 'Nama barang')
            ->setCellValue('G1', 'UOM')
            ->setCellValue('H1', 'Stok')
            ->setCellValue('I1', 'Tanggal masuk');

        $kolom = 3;
        $nomor = 1;
        foreach ($incomingGoods as $result) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('C' . $kolom, $nomor)
                ->setCellValue('D' . $kolom, $result->name_location)
                ->setCellValue('E' . $kolom, $result->code_item)
                ->setCellValue('F' . $kolom, $result->name_item)
                ->setCellValue('G' . $kolom, $result->name_unite_type)
                ->setCellValue('H' . $kolom, $result->stock_incoming_goods)
                ->setCellValue('I' . $kolom, $result->date_of_entry_incoming_goods);

            $kolom++;
            $nomor++;
        }

        $styleArray_title = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('C1:I1')->applyFromArray($styleArray_title);


        $styleArrayColumn = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $kolom = $kolom - 1;
        $spreadsheet->getActiveSheet()->getStyle('C1:I' . $kolom)->applyFromArray($styleArrayColumn);
        $spreadsheet->getActiveSheet()->getStyle('C1:I' . $kolom)
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C1:I' . $kolom)
            ->getAlignment()->setWrapText(true);


        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(35);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $judulLaporan . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
