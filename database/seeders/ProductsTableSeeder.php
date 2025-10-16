<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Product\Models\Product;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $rows = [
            ['FFL-FR-SS304','Frame stainless 304 welded','Mechanical','set','box 40x40/50x50 mm food grade'],
            ['FFL-LEG-ADJ','Kaki leveling SS + foot pad','Mechanical','pcs','M12 height-adjustable'],
            ['FFL-TBL-MAN','Meja fillet manual SS','Mechanical','set','top SS + talang drain + keran cuci'],
            ['FFL-BRD-HDPE','Papan potong HDPE foodgrade','Consumable','pcs','20 mm tebal warna putih'],
            ['FFL-BIN-SS304','Bin troli ikan SS','Mechanical','pcs','GN pan 1/1 with wheels'],
            ['FFL-CNV-MOD','Conveyor sabuk modular foodgrade','Conveyor','set','lebar 400-600 mm'],
            ['FFL-CNV-DRV','Motor conveyor AC + gearbox','Drive','pcs','0.75 kW ratio 1:30'],
            ['FFL-VFD-075','Inverter/VFD 0.75 kW','Electrical','pcs','1ph/3ph 220-380V'],
            ['FFL-SENS-PE','Sensor photoelectric IP67','Sensor','pcs','NPN/PNP diffuse/retro'],
            ['FFL-SENS-PRX','Sensor induktif M18','Sensor','pcs','NO PNP 10 mm'],
            ['FFL-PLC-STD','PLC 24VDC 14/10 I/O','Control','pcs','CPU kecil + expansion ready'],
            ['FFL-HMI-7','HMI 7 inci','Control','pcs','800x480, Ethernet'],
            ['FFL-ESTOP','Emergency stop pushbutton','Safety','pcs','NC twist-release'],
            ['FFL-GUARD-SS','Guarding + cover SS','Safety','set','lembar SS + engsel'],
            ['FFL-PNU-CLAMP','Clamp pneumatik untuk fillet','Pneumatic','pcs','bore Ø32-40 stroke 50'],
            ['FFL-VLV-52','Solenoid valve 5/2 24VDC','Pneumatic','pcs','body 1/4"'],
            ['FFL-FRL-14','Filter Regulator Lubricator','Pneumatic','pcs','1/4" + gauge'],
            ['FFL-FIT-PT','Selang & fitting PU 6 mm','Pneumatic','set','quick fitting'],
            ['FFL-AFIL-HEADCUT','Modul head-cut otomatis','Module','set','knife + guide + penjepit'],
            ['FFL-AFIL-BELLY','Modul belly-split otomatis','Module','set','blade disc + guide rail'],
            ['FFL-BLADE-FIL','Blade/pisau fillet SS','Consumable','pcs','replaceable'],
            ['FFL-SKN-UNIT','Unit skinner ikan lengkap','Module','set','roller tekan + pisau skinner'],
            ['FFL-SKN-KNIFE','Pisau skinner','Consumable','pcs','lebar 300-400 mm'],
            ['FFL-SKN-MOTOR','Motor drive skinner','Drive','pcs','0.37 kW'],
            ['FFL-SKN-SCRP','Scraper/penarik kulit','Mechanical','pcs','adjustable'],
            ['FFL-DRY-CHAM','Kamar pengering ikan','Module','set','SS panel + pintu gasket'],
            ['FFL-DRY-FAN','Kipas sirkulasi dryer','Mechanical','pcs','1.5 kW axial'],
            ['FFL-DRY-HEAT','Heater listrik / burner gas','Utility','set','12-60 kW sesuai kapasitas'],
            ['FFL-DRY-EXH','Kipas exhaust + ducting','Utility','set','Ø300-400'],
            ['FFL-TC-K','Termokopel tipe K','Sensor','pcs','probe 200°C'],
            ['FFL-PID-TC','Kontroler suhu PID','Control','pcs','SSR output'],
            ['FFL-HUM-SNS','Sensor kelembaban','Sensor','pcs','±5% RH'],
            ['FFL-INS-50','Insulasi rockwool 50 mm','Material','m2','density 60 kg/m3'],
            ['FFL-CNV-MESH','Conveyor mesh SS untuk dryer','Conveyor','set','lebar 600 mm'],
            ['FFL-DRAIN-SET','Sistem drain & sifon','Utility','set','pipa SS 1.5"'],
            ['FFL-NOZ-CIP','Nozzle semprot CIP','Utility','pcs','1/4" quick connect'],
            ['BLK-FR-WELD','Rangka press meja T','Mechanical','set','profil 80x80/100x50'],
            ['BLK-TABLE-TS','Meja T-slot steel','Mechanical','pcs','600x800 mm'],
            ['BLK-JIG-BASE','Base plate jig + locating','Tooling','pcs','tool steel + dowel pin'],
            ['BLK-JIG-PLATE','Top plate jig','Tooling','pcs','bore sesuai part'],
            ['BLK-PIN-INS','Pin insert pelepas scrap','Tooling','pcs','custom Ø3-8 mm'],
            ['BLK-GUIDE-SET','Guide pillar & bush','Tooling','set','Ø20-25'],
            ['BLK-STRIPR','Stripper plate + spring','Tooling','set','kompresi die spring'],
            ['BLK-CYL-100','Cylinder pneumatik SMC','Pneumatic','pcs','bore Ø80-100 stroke 100'],
            ['BLK-VAL-52','Solenoid valve 5/2 24VDC','Pneumatic','pcs','brand SMC setara'],
            ['BLK-FRL-38','FRL + regulator + gauge','Pneumatic','pcs','3/8"'],
            ['BLK-FLOW','Flow control valve','Pneumatic','pcs','1/4"'],
            ['BLK-SILENCER','Silencer/muffler','Pneumatic','pcs','1/4"'],
            ['BLK-HOSE-8','Selang PU 8 mm','Pneumatic','roll','100 m'],
            ['BLK-FITT-QC','Fitting QC male/female','Pneumatic','pcs','1/4"'],
            ['BLK-SENS-REED','Reed switch cylinder','Sensor','pcs','24VDC'],
            ['BLK-PRS-SNS','Sensor tekanan udara','Sensor','pcs','0-10 bar'],
            ['BLK-2HAND','Two-hand safety module','Safety','pcs','anti tie-down'],
            ['BLK-LCURT','Light curtain 30 mm','Safety','set','tinggi 600-900 mm'],
            ['BLK-ESTOP','E-Stop & selector','Safety','set','22 mm'],
            ['BLK-PLC-IO','PLC 24VDC + 16 I/O','Control','pcs','expandable'],
            ['BLK-HMI-43','HMI 4.3 inci','Control','pcs','RS485/Ethernet'],
            ['BLK-ENC-IP54','Panel kontrol IP54','Electrical','pcs','400x500x200'],
            ['BLK-PS-24V','Power supply 24V 10A','Electrical','pcs','DIN rail'],
            ['BLK-RELAY','Relay + terminal block','Electrical','set','OMRON/IDEC setara'],
            ['BLK-PEDAL','Foot/hand switch (opsional)','Control','pcs','maintained/monostable'],
            ['BLK-CLAMP-TG','Toggle clamp cepat','Tooling','pcs','holding 250-500 kgf'],
            ['BLK-LIN-GUIDE','Linear guide + slider','Mechanical','set','12-20 mm'],
            ['ALV-TANK-PE','Tanki cairan HDPE','Mechanical','pcs','20-50 L chemical-resistant'],
            ['ALV-FLOAT','Float switch vertical','Sensor','pcs','NO/NC 24V'],
            ['ALV-ULTR-LEV','Sensor level ultrasonik','Sensor','pcs','range 0.2-4 m'],
            ['ALV-CAP-LEV','Sensor level kapasitif','Sensor','pcs','untuk non-metal tank'],
            ['ALV-OVER-SW','Overflow sensor','Sensor','pcs','contact probe'],
            ['ALV-PUMP-PR','Pompa peristaltik','Utility','pcs','0.1-2 L/min'],
            ['ALV-PUMP-DIA','Pompa diafragma','Utility','pcs','12-24 VDC'],
            ['ALV-SOL-VAL','Solenoid valve cairan','Utility','pcs','SS/PTFE seat 1/4"'],
            ['ALV-FLOW','Flow meter mini','Sensor','pcs','turbine 1-10 L/min'],
            ['ALV-CHK-VLV','Check valve PTFE','Utility','pcs','1/4"'],
            ['ALV-NEEDLE','Needle valve fine','Utility','pcs','1/4"'],
            ['ALV-FILTER-IN','Inline strainer','Utility','pcs','80 mesh'],
            ['ALV-TUBE-PTFE','Selang PTFE 6x8','Utility','roll','50 m'],
            ['ALV-QC-FIT','Quick connector PTFE','Utility','pcs','1/4"'],
            ['ALV-BUZZ','Buzzer alarm 24V','Electrical','pcs','85 dB'],
            ['ALV-TWR-LGT','Tower light 3-color','Electrical','pcs','24V'],
            ['ALV-PLC-STD','PLC 24V + analog','Control','pcs','4AI 4AO min'],
            ['ALV-HMI-43','HMI 4.3 inci','Control','pcs','trend display'],
            ['ALV-DLOG','Data logger SD/microSD','Control','pcs','opsional'],
            ['ALV-PS-24V','Power supply 24V 10A','Electrical','pcs','DIN rail'],
            ['ALV-PANEL-IP65','Panel IP65 chem-resist','Electrical','pcs','SS/ABS enclosure'],
            ['ALV-GASKET','Seal/Gland kabel','Electrical','pcs','PG7-PG13.5'],
            ['ALV-CBL-TRAY','Tray/kabel duct','Electrical','set','PVC 60x60'],
            ['ALV-TERM-DIN','Terminal DIN + marker','Electrical','set','blok terminal'],
            ['ALV-ESTOP','E-Stop + key reset','Safety','pcs','22 mm'],
            ['ALV-SPILL-TRAY','Tray tumpahan SS','Safety','pcs','600x400 mm'],
            ['TST-FRAME','Frame uji + base plate','Mechanical','set','profil 80/100 + pelat 20 mm'],
            ['TST-FIX-HND','Jig/fixture pegangan panci','Tooling','set','adjustable clamp'],
            ['TST-FIX-SPN','Jig uji sendok/garpu','Tooling','set','span & radius SNI'],
            ['TST-LC-1K','Load cell 1 kN','Sensor','pcs','kelas C3'],
            ['TST-AMP-SG','Signal conditioner strain','Electrical','pcs','0-10 V / 4-20 mA'],
            ['TST-ACT-LIN','Actuator linear ball-screw','Mechanical','pcs','stroke 300 mm 1 kN'],
            ['TST-SERVO','Servo/stepper + driver','Drive','set','400-750 W'],
            ['TST-DRV-CTRL','Controller motion','Control','pcs','pulse/analog'],
            ['TST-LVDT','Sensor displacement (LVDT)','Sensor','pcs','0-100 mm'],
            ['TST-LIMIT','Limit switch akhir','Sensor','pcs','roller lever'],
            ['TST-WEIGHT','Set beban kalibrasi','Consumable','set','1-20 kg'],
            ['TST-TORQ','Torque meter (opsi)','Sensor','pcs','0-50 N·m'],
            ['TST-HMI-7','HMI 7 inci + recipe','Control','pcs','graph & report'],
            ['TST-PLC-IO','PLC 24V 24 I/O','Control','pcs','high-speed input'],
            ['TST-PS-24V','Power supply 24V 10A','Electrical','pcs','DIN rail'],
            ['TST-PANEL','Panel kontrol IP54','Electrical','pcs','600x600x200'],
            ['TST-SAF-DOOR','Penutup akrilik + kunci','Safety','set','door interlock'],
            ['TST-REL-SAFE','Safety relay/SIL','Safety','pcs','dual channel'],
            ['TST-ESTOP','E-Stop & reset','Safety','set','22 mm'],
            ['TST-GAUGE','Digital force gauge (opsi)','Instrument','pcs','1000 N'],
            ['TST-TIMER','Timer/RTC logging','Control','pcs','rekam waktu uji'],
            ['TST-PRN-USB','Printer mini/USB (opsi)','Accessory','pcs','cetak hasil uji'],
            ['TST-PC-SW','Software logging PC','Software','lic','CSV export'],
            ['TST-MARK','Jangka sorong/height gauge','Instrument','pcs','cek dimensi jig'],
            ['TST-CAL-KIT','Kit kalibrasi load cell','Instrument','set','weight + shackle'],
        ];

        $attrValues = app(AttributeValueRepository::class);

        foreach ($rows as $row) {
            [$code, $prodName, $category, $unit, $spec] = $row;

            $price = $this->randomPriceByCategory($category);

            $existing = Product::where('sku', $code)->first();

            if ($existing) {
                $existing->fill([
                    'name'        => $prodName,
                    'description' => $spec,
                    'quantity'    => 0,
                    'price'       => $price,
                ]);
                $existing->save();
                $product = $existing;
            } else {
                $product = Product::create([
                    'sku'         => $code,
                    'name'        => $prodName,
                    'description' => $spec,
                    'quantity'    => 0,
                    'price'       => $price,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }

            // Save EAV values so UI reads values from attribute_values
            $attrValues->save([
                'entity_type' => 'products',
                'entity_id'   => $product->id,
                'sku'         => $code,
                'name'        => $prodName,
                'description' => $spec,
                'quantity'    => 0,
                'price'       => $price,
            ]);
        }
    }

    private function randomPriceByCategory(string $category): float
    {
        $cat = strtolower($category);

        // Rough IDR ranges per category
        $ranges = [
            'consumable' => [50000, 1500000],
            'mechanical' => [250000, 15000000],
            'conveyor'   => [2000000, 45000000],
            'drive'      => [750000, 10000000],
            'electrical' => [300000, 8000000],
            'sensor'     => [150000, 5000000],
            'control'    => [500000, 12000000],
            'safety'     => [200000, 6000000],
            'pneumatic'  => [150000, 5000000],
            'module'     => [5000000, 80000000],
            'material'   => [100000, 3000000],
            'utility'    => [200000, 10000000],
            'tooling'    => [200000, 8000000],
            'instrument' => [500000, 15000000],
            'accessory'  => [100000, 2000000],
            'software'   => [1000000, 10000000],
        ];

        [$min, $max] = $ranges[$cat] ?? [250000, 10000000];

        // Return to 2 decimals for currency
        $value = mt_rand($min, $max);

        return round($value, 2);
    }
}

