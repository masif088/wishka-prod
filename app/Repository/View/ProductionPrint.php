<?php

namespace App\Repository\View;

use App\Models\TransactionList;
use App\Repository\View;
use Illuminate\Database\Eloquent\Builder;

class ProductionPrint extends TransactionList implements View
{
    protected $table = 'transaction_lists';

    public static function tableSearch($params = null): Builder
    {
        return static::query()->whereHas('transactionStatus', function ($q) {
            $q->where('transaction_status_type_id', '=', 7);
        })->whereHas('transaction', function (Builder $q) {
            $q->whereHas('transactionStatus', function (Builder $q2) {
                $q2->where('transaction_status_type_id', 14);
            });
        });
    }

    public static function tableView(): array
    {
        return [
            'searchable' => false,
        ];
    }

    public static function tableField(): array
    {
        return [
            ['label' => 'No Pesanan', 'sort' => 'id', 'text-align' => 'center'],
            ['label' => 'Produk Pesanan', 'sort' => 'code'],
            ['label' => 'Jumlah Pesanan', 'sort' => 'code'],
            ['label' => 'Mockup', 'sort' => 'code'],
            ['label' => 'Print Proses', 'sort' => 'code'],
            ['label' => 'PIC', 'sort' => 'code'],
            ['label' => 'Progress'],
            ['label' => 'Tindakan'],
        ];
    }

    public static function tableData($data = null): array
    {

        $pic = '';
        $status = $data->transactionStatus->transactionStatusAttachments->where('key', '=', 'pic')->first();
        if (auth()->user()->hasPermissionTo('tambah-pic', 'sanctum')) {
            $link3 = route('transaction.pic-edit', $data->id);
            $pic = "<a href='$link3' class='px-2 py-1 rounded-lg bg-wishka-200 text-wishka-400 text-center text-nowrap'>Input PIC</a>";
        }

        if ($status != null) {
            if ($status->type == 'string') {
                $pic = $status->value;
            }
            if ($status->type != 'string') {
                $pic = new $status->type();
                $pic = $pic->find($status->value)->name;
            }
        }


        $product = $data;
        $name = 'No Product (invalid transaction)';
        $amount = 0;
        if ($product != null) {
            $name = $product->product->title;
            $amount = $product->amount;
        }

        $mockup = $data->transactionStatuses->where('transaction_status_type_id', '=', 3)->first();

        $p2 = '';
        if ($mockup != null) {
            $p2 = $mockup->transactionStatusAttachments->where('key', '=', 'process')->first()->value;

            $link2 = route('transaction.mockup-site-download', $data->id);
            $mockupButton = "<a href='$link2' class='px-2 py-1 rounded-lg bg-wishka-200 text-wishka-400 text-nowrap'>Lihat Mockup</a>";
        } else {
            $mockupButton = 'Mockup tidak ditemukan';
        }
        $progress = "
<select wire:change='changeProduction($data->id,event.target.value)' class='bg-gray-200 appearance-none border-1 border border-gray-100 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none dark:border-primary-light focus:bg-gray-100 dark:bg-dark focus:dark:border-white'>
<option></option>
<option value='3'>Mockup</option>
<option value='4'>Pola</option>
<option value='5'>Sampel</option>
<option value='6'>Potong</option>
<option value='7'>Print</option>
<option value='8'>Pasang Label</option>
<option value='9'>Jahit</option>
<option value='10'>Quality Control</option>
<option value='11'>Packing</option>
<option value='12'>Menunggu Pembayaran</option>
</select>";
        $link4 = route('transaction.image-gallery', $data->id);
        $link5 = route('transaction.image-edit', $data->id);

        return [
            ['type' => 'raw_html', 'text-align' => 'center', 'data' => $data->transaction->uid.'<br>'.$data->uid],
            ['type' => 'string', 'text-align' => 'start', 'data' => $name],
            ['type' => 'string', 'text-align' => 'center', 'data' => $amount.'pcs'],
            ['type' => 'raw_html', 'data' => $mockupButton],
            ['type' => 'raw_html', 'data' => $p2],
            ['type' => 'raw_html', 'data' => $pic],
            ['type' => 'raw_html', 'data' => $progress],
            ['type' => 'raw_html', 'data' => "
            <div class='text-xl flex gap-1'>
            <a href='$link5' class='py-1 px-2 bg-wishka-600 text-white rounded-lg'><i class='ti ti-photo-up'></i></a>
            <a href='$link4' class='py-1 px-2 bg-wishka-600 text-white rounded-lg'><i class='ti ti-album'></i></a>
            </div>
            "],
        ];
    }
}
