<?php
class CreateLabelController extends AppController {
	private $userUid;
    private $userCabangId;
    private $userCabangCode;
    private $userCabangName;

	protected function Initialize() {
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userCabangCode = $this->persistence->LoadState("cabang_kode");
        $this->userCabangName = $this->persistence->LoadState("cabang_name");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

    public function index() {
        $router = Router::GetInstance();
        $settings = array();
        $settings["columns"][] = array("name" => "a.bid", "display" => "ID", "width" => 0);
        $settings["columns"][] = array("name" => "a.bkode", "display" => "Kode Barang", "width" => 80);
        $settings["columns"][] = array("name" => "a.bbarcode", "display" => "Bar Code", "width" => 100);
        $settings["columns"][] = array("name" => "a.bnama", "display" => "Nama Barang", "width" => 300);
        //$settings["columns"][] = array("name" => "a.bnama1", "display" => "Nama Barang2", "width" => 250);
        //$settings["columns"][] = array("name" => "a.bnamaskt", "display" => "Nama SKT", "width" => 250);
        //$settings["columns"][] = array("name" => "a.bjenis", "display" => "Jenis", "width" => 60);
        $settings["columns"][] = array("name" => "c.kelompok", "display" => "Kelompok", "width" => 160);
        $settings["columns"][] = array("name" => "b.divisi", "display" => "Merk", "width" => 60);
        $settings["columns"][] = array("name" => "a.bsatkecil", "display" => "Satuan", "width" => 40);
        $settings["columns"][] = array("name" => "format(a.bhargajual1,0)", "display" => "Harga Eceran", "width" => 80,"align" => "right");
        //$settings["columns"][] = array("name" => "a.bsatbesar", "display" => "Kemasan", "width" => 40);
        //$settings["columns"][] = array("name" => "format(a.bisisatkecil,0)", "display" => "Isi", "width" => 30,"align" => "right");
        $settings["columns"][] = array("name" => "a.bsnama", "display" => "Supplier", "width" => 150);
        //$settings["columns"][] = array("name" => "a.bminstock", "display" => "Minimum", "width" => 40,"align" => "right");
        //$settings["columns"][] = array("name" => "if(a.bisallowmin = 1,'Boleh','Tidak')", "display" => "Minus", "width" => 50);
        $settings["columns"][] = array("name" => "if(a.bisaktif = 1,'Aktif','Tidak')", "display" => "Is Aktif", "width" => 50);
        //$settings["columns"][] = array("name" => "if(a.item_level = 0,'Global',if(a.item_level = 1,'Company','Private'))", "display" => "Level", "width" => 40);
        //$settings["columns"][] = array("name" => "a.def_cabang_code", "display" => "Def. Cabang", "width" => 80);
        //$settings["columns"][] = array("name" => "a.bketerangan", "display" => "Keterangan", "width" =>250);

        $settings["filters"][] = array("name" => "a.bnama", "display" => "Nama Barang");
        $settings["filters"][] = array("name" => "a.bkode", "display" => "Kode Barang");
        $settings["filters"][] = array("name" => "a.bbarcode", "display" => "Bar Code");
        $settings["filters"][] = array("name" => "a.bjenis", "display" => "Jenis");
        $settings["filters"][] = array("name" => "b.divisi", "display" => "Merk");
        $settings["filters"][] = array("name" => "c.kelompok", "display" => "Kelompok");
        $settings["filters"][] = array("name" => "a.bsnama", "display" => "Supplier");
        $settings["filters"][] = array("name" => "if(a.bisaktif = 1,'Aktif','Tidak')", "display" => "Status Aktif");
        $settings["filters"][] = array("name" => "if(a.item_level = 0,'Global',if(a.item_level = 1,'Company','Private'))", "display" => "Level");

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Daftar Barang";

            if ($acl->CheckUserAccess("master.items", "view")) {
                $settings["actions"][] = array("Text" => "Create Label", "Url" => "inventory/createlabel/create/%s", "Class" => "bt_edit", "ReqId" => 2,
                    "Error" => "Mohon memilih items terlebih dahulu sebelum proses create label","Confirm" => "");
            }

            $settings["def_order"] = 3;
            $settings["def_filter"] = 0;
            $settings["singleSelect"] = false;

        } else {
            $settings["from"] = "vw_m_barang AS a Left Join m_barang_divisi AS b On a.bdivisi = b.kode Left Join m_barang_kelompok AS c On a.bkelompok = c.kode";
            $settings["where"] = "a.is_deleted = 0 And a.bisaktif = 1 And a.cabang_id = ".$this->userCabangId;
        }

        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }

	public function create() {
        require_once(MODEL . "master/items.php");
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di approve !");
            redirect_url("ar.invoice");
            return;
        }
        $aitems = array();
        foreach ($ids as $id) {
            $items = new Items($id);
            $aitems[] = array($id,$items->Bkode,$items->Bbarcode,$items->Bnama,$items->Bsatkecil,$items->Bhargajual1);
        }
        $this->Set("aitems",$aitems);
        $this->Set("cabang_kode",$this->userCabangCode);
        $this->Set("cabang_name",$this->userCabangName);
	}

	public function lblprint(){
        require_once(LIBRARY . "PHPBarCode/BarcodeGenerator.php");
        require_once(LIBRARY . "PHPBarCode/BarcodeGeneratorHTML.php");
        require_once(LIBRARY . "PHPBarCode/BarcodeGeneratorJPG.php");
        require_once(LIBRARY . "PHPBarCode/BarcodeGeneratorPNG.php");

        require_once(MODEL . "master/items.php");

        if (count($this->postData) > 0) {
            $aitemid = $this->GetPostValue("litemid");
            $apilih = $this->GetPostValue("lpilih");
            $altype = $this->GetPostValue("ltype");
            $alqty = $this->GetPostValue("lqty");
            if (count($aitemid) > 0) {
                //create table
                $dtx = 0;
                $qty = 0;
                $bcd = null;
                $prc = 0;
                $bnm = null;
                print("<table cellspacing='3' cellpadding='3'>");
                foreach ($aitemid as $id) {
                    if (isset($apilih[$dtx])) {
                        $items = new Items($id);
                        $bcd = $items->Bkode;
                        $bnm = left($items->Bnama,20);
                        $prc = $items->Bhargajual1;
                        $qty = $alqty[$dtx];
                        $col = 0;
                        $cnt = 0;
                        while ($cnt < $qty) {
                            $col++;
                            $generator = new \Picqer\Barcode\BarcodeGeneratorJPG();
                            if ($col == 1) {
                                print("<tr>");
                            }
                            print('<td>');
                            printf('<font size="1">%s<br>',$this->userCabangCode);
                            printf('<font size="1">%s<br>',$bnm);
                            print('<img width="110px" height="15px" src="data:image/jpg;base64,' . base64_encode($generator->getBarcode($bcd, $generator::TYPE_CODE_128)) . '">');
                            printf('<br><font size="2">%s',$bcd);
                            printf('<font size="3"><br>Rp. %s,-',number_format($prc,0));
                            print('</td>');
                            if ($col == 3) {
                                print("</tr>");
                                $col = 0;
                            }
                            $cnt++;
                        }
                    }
                    $dtx++;
                }
                print('</table>');
            }
        }
    }

}

// End of file: itemuom_controller.php
