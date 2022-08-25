<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExitItem;

use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Gate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportItemOutController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $exitItem = ExitItem::joinAllTable();

            return Datatables::of($exitItem)
                ->addIndexColumn()
                ->addColumn('picture_item', function ($exitItem) {
                    $pictureItem = $exitItem->picture_item != null ? public_path() . '/image/item/' . $exitItem->picture_item : false;
                    if (file_exists($pictureItem)) {
                        $pictureItemFix = asset('image/item/' . $exitItem->picture_item);
                    } else {
                        $pictureItemFix = asset('image/item/default.png');
                    }
                    $output = '
                    <a data-gallery="photoviewer" data-title="' . $exitItem->name_item . '" href="' . $pictureItemFix . '" alt="gambar-' . $exitItem->id . '" data-group="a">
                        <img src="' . $pictureItemFix . '" class="w-100 img-thumbnail"></img>
                    </a>
                    ';
                    return $output;
                })

                ->rawColumns(['picture_item'])
                ->make();
        }
        return view('admin.reportItemOut.index');
    }

    public function export(Request $request)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        $exitItem = ExitItem::joinAllTable();

        $spreadsheet = new Spreadsheet;
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('C1', 'No.')
            ->setCellValue('D1', 'Lokasi')
            ->setCellValue('E1', 'Kode barang')
            ->setCellValue('F1', 'Nama barang')
            ->setCellValue('G1', 'UOM')
            ->setCellValue('H1', 'Stok')
            ->setCellValue('I1', 'Tanggal Keluar');

        $kolom = 3;
        $nomor = 1;
        foreach ($exitItem as $result) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('C' . $kolom, $nomor)
                ->setCellValue('D' . $kolom, $result->name_location)
                ->setCellValue('E' . $kolom, $result->code_item)
                ->setCellValue('F' . $kolom, $result->name_item)
                ->setCellValue('G' . $kolom, $result->name_unite_type)
                ->setCellValue('H' . $kolom, $result->stock_exit_item)
                ->setCellValue('I' . $kolom, $result->out_date_exit_item);

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
        header('Content-Disposition: attachment;filename="Laporan_Barang_keluar.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
