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
            $incomingGoods = IncomingGoods::joinAllTable();
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

        $incomingGoods = IncomingGoods::joinAllTable();

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
        header('Content-Disposition: attachment;filename="Laporan_Barang_Masuk.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
