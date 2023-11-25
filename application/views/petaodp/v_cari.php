<?php if (!defined('BASEPATH')) exit('No direct script acess allowed'); ?>
<link href="<?= base_url() ?>assets_style/leaflet/leaflet.css" rel="stylesheet">
<script src="<?= base_url() ?>assets_style/leaflet/leaflet.js"></script>
<script src="<?= base_url() ?>assets_style/leaflet/leaflet.ajax.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.css">
<script src="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.js"></script>
<link href="<?= base_url() ?>assets_style/leaflet/dist/Control.MiniMap.min.css" rel="stylesheet">
<script src="<?= base_url() ?>assets_style/leaflet/dist/Control.MiniMap.min.js"></script>



<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-edit" style="color:green"> </i> Peta Data Pelanggan
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('dashboard'); ?>"><i class="fa fa-dashboard"></i>&nbsp; Dashboard</a></li>
            <li class="active"><i class="fa fa-file-text"></i>&nbsp; Peta</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                        <div class="container">
                            <?php if (empty($odp)) { ?>
                                <?php echo '<script>alert("UNIT TIDAK DITEMUKAN");window.location="' . base_url('petaodp') . '"</script>'; ?>
                            <?php } else { ?>
                                <h3 class="my-2 text-gray-800">Anda Mencari Unit : <?= $keyword ?></h3>
                            <?php } ?>
                            <div class="form-inline row">
                                <h5 class="col-sm-3"><b>Cari Unit Kebun Kelapa Sawit :</b></h5>
                                <div class="col-sm-6">
                                    <?php echo form_open('petaodp/pencarian') ?>
                                    <select name="keyword" class="form-control">
                                        <?php foreach ($daftar_odp as $df) { ?>
                                            <option value="<?= $df['nama_odp']; ?>" <?php if ($df['nama_odp'] == $keyword) {
                                                                                        echo 'selected';
                                                                                    } ?>><?= $df['nama_odp']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <button type="submit" class="btn btn-success">Cari</button>
                                    <?php echo form_close() ?>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="container-fluid">
                            <div id="map" style="width: 100%; height: 450px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    var odp = L.layerGroup();
    var peta1 = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
            '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
            'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        id: 'mapbox/streets-v11',
        accessToken: 'pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw'
    });


    var peta2 = L.tileLayer('http://www.google.cn/maps/vt?lyrs=s@189&gl=cn&x={x}&y={y}&z={z}', {
        attribution: 'google'
    });

    var peta3 = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });

    <?php foreach ($odp as $o) { ?>
        var map = L.map('map', {
            center: [<?= $o->latitude ?>, <?= $o->longitude ?>],
            zoom: 14,
            layers: [peta2, odp]
        });
    <?php } ?>

    var baseLayers = {
        "Grayscale": peta1,
        "Satelite": peta2,
        "Streets": peta3
    };

    var overlays = {
        "Odp": odp,
        "Panen": panen,
        "Pemeliharaan": pemeliharaan,
        "Jalan": jalan,
        "Pencurian": pencurian,
        "Bencana Alam": bencana,
    };

    <?php foreach ($odp as $o) { ?>
        var homebutton = L.easyButton('fa-home fa-lg', function() {
            map.setView([<?= $o->latitude ?>, <?= $o->longitude ?>], 14);
        }, 'home position', {
            position: 'topright'
        });
    <?php } ?>
    L.control.layers(baseLayers, overlays).addTo(map);
    L.control.scale().addTo(map);
    homebutton.addTo(map);
    L.Control.geocoder().addTo(map);

    var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    var osmAttrib = 'Map data &copy; OpenStreetMap contributors';
    var osm2 = new L.TileLayer(osmUrl, {
        minZoom: 0,
        maxZoom: 13,
        attribution: osmAttrib
    });
    var miniMap = new L.Control.MiniMap(osm2, {
        toggleDisplay: true
    }).addTo(map);

    //Odp
    <?php foreach ($odp as $o) { ?>
        L.geoJSON({
            "type": "FeatureCollection",
            "features": [<?= $o->odp_geojson; ?>]
        }, {
            style: {
                color: "black",
                fillOpacity: 0.4,
                weight: 1.5,
                opacity: 1,
                fillColor: "<?= $o->warna ?>"
            },
        }).addTo(odp).on('click', function() {
            Swal.fire({
                imageUrl: '<?= base_url('assets_style/file/' .  $isi->foto) ?>',
                title: '<span class="text-uppercase">UNIT <?= $o->nama_odp ?></span>',
                text: "<?= $isi->alamat ?>",
                imageHeight: 200,
                showCancelButton: true,
                cancelButtonText: "Tutup",
                confirmButtonText: "Lihat Detail",
                confirmButtonColor: "Green"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = '<?= base_url('odp/detail/' . $isi->id_odp); ?>';
                }
            })
        });
    <?php } ?>

    //Panen
    <?php foreach ($panen as $isi) { ?>
        L.geoJSON({
            "type": "FeatureCollection",
            "features": [<?= $isi->panen_geojson; ?>]
        }, {
            style: {
                color: "<?= $isi->warna ?>",
                fillOpacity: 0.7,
                weight: 1.5,
                opacity: 1,
                fillColor: "<?= $isi->warna ?>"
            },
        }).addTo(panen).on('click', function() {
            Swal.fire({
                imageUrl: '<?= base_url('assets_style/file/' .  $isi->foto) ?>',
                title: '<span class="text-uppercase">Panen Unit <?= $isi->lokasi ?></span>',
                text: 'Tanggal : <?= date('d M Y', strtotime($isi->tanggal)) ?>',
                imageHeight: 200,
                showCancelButton: true,
                cancelButtonText: "Tutup",
                confirmButtonText: "Lihat Detail",
                confirmButtonColor: "Green"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = '<?= base_url('panen/detail/' . $isi->id_panen) ?>';
                }
            })
        });
    <?php } ?>

    //Pemeliharaan
    <?php foreach ($pemeliharaan as $isi) { ?>
        L.geoJSON({
            "type": "FeatureCollection",
            "features": [<?= $isi->pemeliharaan_geojson; ?>]
        }, {
            style: {
                color: "<?= $isi->warna ?>",
                fillOpacity: 0.7,
                weight: 1.5,
                opacity: 1,
                fillColor: "<?= $isi->warna ?>"
            },
        }).addTo(pemeliharaan).on('click', function() {
            Swal.fire({
                imageUrl: '<?= base_url('assets_style/file/' .  $isi->foto) ?>',
                title: '<span class="text-uppercase">Pemeliharaan Unit <?= $isi->lokasi ?></span>',
                text: 'Tanggal : <?= date('d M Y', strtotime($isi->tanggal)) ?>',
                imageHeight: 200,
                showCancelButton: true,
                cancelButtonText: "Tutup",
                confirmButtonText: "Lihat Detail",
                confirmButtonColor: "Green"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = '<?= base_url('pemeliharaan/detail/' . $isi->id_pemeliharaan) ?>';
                }
            })
        });
    <?php } ?>

    //Jalan
    <?php foreach ($jalan as $isi) { ?>
        L.geoJSON({
            "type": "FeatureCollection",
            "features": [<?= $isi->jalan_geojson; ?>]
        }, {
            style: {
                color: "<?= $isi->warna ?>",
                weight: <?= $isi->ketebalan ?>,
            },
        }).addTo(jalan).on('click', function() {
            Swal.fire({
                imageUrl: '<?= base_url('assets_style/file/' .  $isi->foto) ?>',
                title: '<span class="text-uppercase">Jalan Unit <?= $isi->lokasi ?></span>',
                imageHeight: 200,
                showCancelButton: true,
                cancelButtonText: "Tutup",
                confirmButtonText: "Lihat Detail",
                confirmButtonColor: "Green"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = '<?= base_url('jalan/detail/' . $isi->id_jalan) ?>';
                }
            })
        });
    <?php } ?>

    //Pencurian
    <?php foreach ($pencurian as $isi) { ?>
        L.marker([<?= $isi->latitude ?>, <?= $isi->longitude ?>], {
            icon: L.icon({
                iconUrl: '<?= base_url('assets_style/marker/marker-red.png')  ?>',
                iconSize: [27, 35],
            })
        }).addTo(pencurian).on('click', function() {
            Swal.fire({
                imageUrl: '<?= base_url('assets_style/file/' .  $isi->foto) ?>',
                title: '<span class="text-uppercase">Lokasi Pencurian : <?= $isi->lokasi ?></span>',
                text: 'Tanggal : <?= date('d M Y', strtotime($isi->tanggal)) ?>',
                imageHeight: 200,
                showCancelButton: true,
                cancelButtonText: "Tutup",
                confirmButtonText: "Lihat Detail",
                confirmButtonColor: "Green"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = '<?= base_url('pencurian/detail/' . $isi->id_pencurian) ?>';
                }
            })
        });
    <?php } ?>

    //Bencana
    <?php foreach ($bencana as $isi) { ?>
        L.geoJSON({
            "type": "FeatureCollection",
            "features": [<?= $isi->bencana_geojson; ?>]
        }, {
            style: {
                color: "<?= $isi->warna ?>",
                fillOpacity: 0.7,
                weight: 1.5,
                opacity: 1,
                fillColor: "<?= $isi->warna ?>"
            },
        }).addTo(bencana).on('click', function() {
            Swal.fire({
                imageUrl: '<?= base_url('assets_style/file/' .  $isi->foto) ?>',
                title: '<span class="text-uppercase">Lokasi Bencana : <?= $isi->lokasi ?></span>',
                html: '<p class="small my-1">Nama Bencana : <?= $isi->nama_odp; ?> <br> Tanggal : <?= date('d M Y', strtotime($isi->tanggal)) ?></p>',
                imageHeight: 200,
                showCancelButton: true,
                cancelButtonText: "Tutup",
                confirmButtonText: "Lihat Detail",
                confirmButtonColor: "Green"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = '<?= base_url('bencana/detail/' . $isi->id_bencana) ?>';
                }
            })
        });
    <?php } ?>
</script>