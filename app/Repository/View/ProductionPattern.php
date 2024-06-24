<?php

namespace App\Repository\View;

use App\Models\Transaction;
use App\Models\TransactionList;
use App\Repository\View;
use Illuminate\Database\Eloquent\Builder;

class ProductionPattern extends TransactionList implements View
{
    protected $table = 'transaction_lists';

    public static function tableSearch($params = null): Builder
    {
        $query = $params['query'];

        return empty($query) ? static::query()->whereHas('transactionStatus', function ($q) {
            $q->where('transaction_status_type_id', '=', 4);
        }) : static::query();
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
            ['label' => 'Nama Customer'],
//            ['label' => 'Upload Mockup', 'text-align' => 'center'],
            ['label' => 'PIC', 'text-align' => 'center'],
            ['label' => 'Ubah Progress', 'text-align' => 'center'],
            ['label' => 'Tindakan', 'text-align' => 'center'],
        ];
    }

    public static function tableData($data = null): array
    {


        $process = 'Telah terkirim';

        $edit = '';
        $download = '';
        $progress = "<select wire:change='changeProduction($data->id,event.target.value)' class='bg-gray-200 appearance-none border-1 border border-gray-100 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none dark:border-primary-light focus:bg-gray-100 dark:bg-dark focus:dark:border-white'><option></option><option value='3'>Mockup</option><option value='4'>Pola</option><option value='5'>Sampel</option><option value='6'>Potong</option><option value='7'>Print</option><option value='8'>Pasang Label</option><option value='9'>Jahit</option><option value='10'>Quality Control</option><option value='11'>Packing</option><option value='12'>Menunggu Pembayaran</option></select>";
        $link3=route('transaction.pic-edit',$data->id);

        $pic = $data->transactionStatus->transactionStatusAttachments->where('key', '=', 'pic')->first();
        if ($pic==null){
            $pic = "<a href='$link3' class='px-2 py-1 rounded-lg bg-wishka-200 text-wishka-400 text-center'>Input PIC</a>";
        }else{
            $user = new $pic->type();
            $pic = $user->find($pic->value)->name;
        }


        return [
            ['type' => 'raw_html', 'text-align' => 'center', 'data' => $data->transaction->uid.'<br>'.$data->uid],
            ['type' => 'raw_html', 'data' => $data->transaction->customer->name." <br> <span class='text-sm'>".$data->transaction->customer->email.'</span>'],
            ['type' => 'raw_html', 'text-align' => 'center', 'data' => $pic],
            ['type' => 'raw_html', 'text-align' => 'center', 'data' => $progress],
            ['type' => 'raw_html', 'text-align' => 'center', 'data' => "
            <span class='text-xl font-black'>
            $edit
            $download
            </span>
            "],
        ];
    }
}
