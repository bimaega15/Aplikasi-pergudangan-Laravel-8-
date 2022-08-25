<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockStore;
use Barryvdh\DomPDF\Facade\Pdf as Dompdf;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Gate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class ReportStockController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $stockStore = StockStore::leftJoin('location as lc', 'lc.id', '=', 'stock_store.location_id')
                ->leftJoin('item as im', 'im.id', '=', 'stock_store.item_id')
                ->leftJoin('unite_type as utp', 'utp.id', '=', 'stock_store.unite_type_id')
                ->select('*', 'stock_store.id as id_stock_store')
                ->get();

            return Datatables::of($stockStore)
                ->addIndexColumn()
                ->addColumn('name_location', function ($stockStore) {
                    $output = $stockStore->name_location;
                    return $output;
                })
                ->addColumn('code_item', function ($stockStore) {
                    $output = $stockStore->code_item;
                    return $output;
                })
                ->addColumn('name_item', function ($stockStore) {
                    $output = $stockStore->name_item;
                    return $output;
                })
                ->addColumn('picture_item', function ($stockStore) {
                    $pictureItem = $stockStore->picture_item != null ? public_path() . '/image/item/' . $stockStore->picture_item : false;
                    if (file_exists($pictureItem)) {
                        $pictureItemFix = asset('image/item/' . $stockStore->picture_item);
                    } else {
                        $pictureItemFix = asset('image/item/default.png');
                    }
                    $output = '
                    <a data-gallery="photoviewer" data-title="' . $stockStore->name_item . '" href="' . $pictureItemFix . '" alt="gambar-' . $stockStore->id . '" data-group="a">
                        <img src="' . $pictureItemFix . '" class="w-100 img-thumbnail"></img>
                    </a>
                    ';
                    return $output;
                })
                ->addColumn('name_unite_type', function ($stockStore) {
                    $output = $stockStore->name_unite_type;
                    return $output;
                })
                ->addColumn('store_stock_store', function ($stockStore) {
                    $output = number_format($stockStore->store_stock_store, 0);
                    return $output;
                })
                ->rawColumns(['picture_item'])
                ->make();
        }
        return view('admin.reportStock.index');
    }
    public function export()
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        $stockStore = StockStore::leftJoin('location as lc', 'lc.id', '=', 'stock_store.location_id')
            ->leftJoin('item as im', 'im.id', '=', 'stock_store.item_id')
            ->leftJoin('unite_type as utp', 'utp.id', '=', 'stock_store.unite_type_id')
            ->select('*', 'stock_store.id as id_stock_store')
            ->get();

        $spreadsheet = new Spreadsheet;
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('C1', 'No.')
            ->setCellValue('D1', 'Lokasi')
            ->setCellValue('E1', 'Kode barang')
            ->setCellValue('F1', 'Nama barang')
            ->setCellValue('G1', 'UOM')
            ->setCellValue('H1', 'Stok');

        $kolom = 3;
        $nomor = 1;
        foreach ($stockStore as $result) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('C' . $kolom, $nomor)
                ->setCellValue('D' . $kolom, $result->name_location)
                ->setCellValue('E' . $kolom, $result->code_item)
                ->setCellValue('F' . $kolom, $result->name_item)
                ->setCellValue('G' . $kolom, $result->name_unite_type)
                ->setCellValue('H' . $kolom, $result->store_stock_store);

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
        $spreadsheet->getActiveSheet()->getStyle('C1:H1')->applyFromArray($styleArray_title);


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
        $spreadsheet->getActiveSheet()->getStyle('C1:H' . $kolom)->applyFromArray($styleArrayColumn);
        $spreadsheet->getActiveSheet()->getStyle('C1:H' . $kolom)
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C1:H' . $kolom)
            ->getAlignment()->setWrapText(true);


        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Laporan_Stock_Barang.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
