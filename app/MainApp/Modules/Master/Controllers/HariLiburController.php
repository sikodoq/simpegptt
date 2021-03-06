<?php

namespace App\MainApp\Modules\Master\Controllers;

use Illuminate\Http\Request; 
use Facades\App\MainApp\Repositories\Master;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;

use App\Base\BaseController;

class HariLiburController extends BaseController
{
    /**
     * GET - List data
     * 
     * @param Request $request
     *      orderBy
     *      orderType
     *      q
     *      offset
     *      limit
     */
    public function index(Request $request)
    {
        if(!UserAuth::hasAccess('Master.harilibur')){
            $this->response = redirect()->route('dashboard');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }
        
        $this->response = 'hari_libur.list';

        $orderBy = false;
        $filter = //untuk filter ke repo
        $paginationParams = //untuk parameter get ke pagination
            [
                'q'=>$request->input('q', null)
            ];

        //jika menyertakan order by
        if($request->input('orderBy', null)){
            $orderBy = [$request->input('orderBy'),$request->input('orderType','ASC')];

            $paginationParams['orderBy'] =$orderBy[0];
            $paginationParams['orderType'] =$orderBy[1];
        }
        
        $limit['offset'] = $request->input('offset', 0);
        $limit['limit'] = $request->input('limit', 10);

        $this->output['data'] = Master::listHariLibur(
            $filter, $limit['offset'], $limit['limit'],$orderBy
        );
 

        //generate pagination
        // $this->output['viewdata']['pagination'] = Master::getPaginationJabatan(route('master.jabatan.list', $paginationParams));
        
        return $this->done();
    }

    /**
     * GET - form add new
     */
    public function addNew(Request $request)
    {
        $this->response = 'hari_libur.form';
        
        return $this->done();
    }

    /**
     * POST - save data baru
     */
    public function create(Request $request)
    {
        $this->response = redirect()->route('master.hari_libur.list');

        $data = $request->all();
        if($data){
            $this->output['message'] = 'Data berhasil disimpan';
            $this->output['data'] = Master::createHariLibur($data);            
        }else{
            $this->setError('Data Not Found');
            $this->response = redirect()->route('master.hari_libur.addNew');
        }
        
        return $this->done();

    }
    /**
     * GET - get 1 data atau memunculkan form edit
     */
    public function edit(Request $request)
    {
        $this->response = 'hari_libur.form';
        
        $id = $request->route('id');

        $this->output['data'] = Master::getHariLibur($id);
        if(!$this->output['data']){
            $this->setError('Data Not Found');
            //jika error redirect ke halaman list
            $this->response = redirect()->route('master.hari_libur.list');
        }

        return $this->done();

    }
    /**
     * PUT - update edit data
     */
    public function update(Request $request)
    {
        $this->response = redirect()->route('master.hari_libur.list');
        
        $id = $request->route('id');
        $data = $request->all();
        
        $this->output['message'] = 'Data berhasil diupdate';
        $this->output['data'] = Master::updateHariLibur(['id',$id],$data);  
        if(!$this->output['data']){
            $this->setError(Master::error());
            //jika error redirect ke halaman form
            $this->response = redirect()->route('master.hari_libur.edit',['id'=>$id]);
        }        
        return $this->done();

    }

    /**
     * DELETE - delete data
     */
    public function delete(Request $request) 
    {        
        $this->response = redirect()->route('master.hari_libur.list');

        $id = $request->route('id');

        if(Master::deleteHariLibur($id)){
            $this->output['message'] = 'Data berhasil ';
        }else{
            $this->setError(Master::error());
        }
        
        return $this->done();

    }
}