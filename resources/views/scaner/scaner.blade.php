<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maju Jaya - Sistem QR Gudang 1</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <style>
        body {
            background: #f0fdf4;
        }

        .glass-card {
            background: white;
            border-radius: 2rem;
            border: 1px solid #dcfce7;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .05);
        }
    </style>
</head>

<body class="p-4 md:p-10">

    <div class="max-w-5xl mx-auto space-y-6">

        <!-- HEADER -->
        <div class="flex justify-between items-center bg-white p-6 rounded-3xl border border-green-100">
            <div>
                <h1 class="text-2xl font-black text-green-800">MAJU JAYA <span
                        class="font-light text-green-500">FURNITURE</span></h1>
                <p class="text-[10px] uppercase text-gray-400">Penerima: <b class="text-green-600">Gudang 1</b></p>
            </div>
            <div id="notaDisplay" class="font-mono text-sm font-bold bg-green-50 text-green-700 px-4 py-2 rounded-xl">
            </div>
        </div>

        <!-- SCANNER -->
        <div class="grid md:grid-cols-2 gap-8">

            <div class="glass-card p-6 text-center">
                <label class="text-[10px] font-black text-green-700 uppercase tracking-widest">
                    QR Scanner Kamera Aktif
                </label>

                <div id="reader" class="mt-4 rounded-2xl overflow-hidden border border-green-200"></div>

                <p class="text-[9px] text-gray-400 mt-3 uppercase">Arahkan kamera ke QR</p>
            </div>

            <div class="glass-card p-6">
                <label class="text-xs font-black text-gray-400 uppercase mb-2 block">Supplier Finishing</label>
                <select id="supplierSelect" class="w-full p-4 rounded-2xl border-2 border-gray-100 font-bold">
                    <option>Bpk. Bambang Pamungkas</option>
                    <option>Bpk. Budi Setiawan</option>
                    <option>Bpk. Agus Santoso</option>
                    <option>Bpk. Slamet Riyadi</option>
                    <option>Solonet</option>
                </select>
            </div>

        </div>

        <!-- CART -->
        <div class="glass-card">
            <div class="p-6 bg-gray-50 flex justify-between">
                <h3 class="font-black text-sm text-gray-700 uppercase">Cart List Product</h3>
                <span id="unitBadge" class="bg-green-600 text-white text-[10px] px-4 py-1 rounded-full">0 UNIT</span>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-white text-[10px] uppercase text-gray-400">
                    <tr>
                        <th class="p-4">Kode QR</th>
                        <th class="p-4">Barang</th>
                        <th class="p-4 text-center">Qty</th>
                        <th class="p-4">Warna</th>
                        <th class="p-4">Order</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="cartTableBody"></tbody>
            </table>

            <div id="noDataRow" class="p-16 text-center text-gray-300 italic">
                Silahkan scan QR barang...
            </div>
        </div>

        <button onclick="exportToPDF()"
            class="w-full bg-green-700 hover:bg-green-800 text-white font-black py-6 rounded-3xl uppercase tracking-widest">
            Simpan & Export PDF
        </button>

    </div>

    <script>
        // DATABASE
        const db = [{
                id: "TRX52879071",
                nama: "Buffet Acajou 160",
                warna: "Mahogany",
                order: "ORD-992"
            },
            {
                id: "10",
                nama: "Buffet Teak 180",
                warna: "Natural",
                order: "ORD-881"
            },
            {
                id: "QR-15134002",
                nama: "Meja Makan Teak",
                warna: "Dark Brown",
                order: "ORD-775"
            },
            {
                id: "2024",
                nama: "Kursi Makan Teak",
                warna: "Walnut",
                order: "ORD-663"
            },
            {
                id: "MJ-001",
                nama: "Lemari 2 Pintu",
                warna: "White Oak",
                order: "ORD-449"
            }
        ];

        let cart = [];
        const nota = "NOTA-" + Math.floor(Math.random() * 90000 + 10000);
        notaDisplay.innerText = "NO. NOTA: " + nota;

        // SCAN RESULT
        function onScanSuccess(text) {
            const product = db.find(p => p.id === text);
            if (!product) return;

            const exist = cart.find(c => c.id === product.id);
            exist ? exist.qty++ : cart.push({
                ...product,
                qty: 1
            });
            render();
        }

        // CAMERA INIT
        new Html5Qrcode("reader").start({
                facingMode: "environment"
            }, {
                fps: 10,
                qrbox: 250
            },
            onScanSuccess
        );

        // RENDER TABLE
        function render() {
            const body = document.getElementById("cartTableBody");
            const empty = document.getElementById("noDataRow");
            const badge = document.getElementById("unitBadge");

            empty.classList.toggle("hidden", cart.length > 0);
            body.innerHTML = cart.map((c, i) => `
    <tr class="border-b">
        <td class="p-4 font-mono text-green-700">${c.id}</td>
        <td class="p-4">${c.nama}</td>
        <td class="p-4 text-center font-black">${c.qty}</td>
        <td class="p-4 italic text-gray-400">${c.warna}</td>
        <td class="p-4 font-mono text-xs">${c.order}</td>
        <td class="p-4 text-right">
            <button onclick="cart.splice(${i},1);render()" class="text-red-400">Hapus</button>
        </td>
    </tr>`).join("");

            badge.innerText = cart.reduce((a, b) => a + b.qty, 0) + " UNIT";
        }

        // EXPORT PDF
        function exportToPDF() {
            if (cart.length === 0) {
                alert("Silahkan scan QR barang terlebih dahulu!");
                return;
            }
            
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const supplier = document.getElementById('supplierSelect').value;
            const date = new Date().toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            // Header
            doc.setFontSize(10);
            doc.text("MAJU JAYA FURNITURE", 15, 15);
            doc.text("Gudang 1 - Sistem QR", 15, 20);
            
            doc.setFontSize(22);
            doc.setFont("helvetica", "bold");
            doc.text("SURAT JALAN", 195, 20, { align: "right" });

            // Informasi
            doc.setFontSize(11);
            doc.setFont("helvetica", "normal");
            doc.text(`Kepada: Gudang 1`, 15, 35);
            doc.text(`Supplier: ${supplier}`, 15, 40);
            doc.text(`No. Nota: ${nota}`, 140, 35);
            doc.text(`Tanggal: ${date}`, 140, 40);

            // Tabel barang
            const tableRows = cart.map(item => [
                item.id,
                item.nama,
                item.qty,
                item.warna,
                item.order
            ]);
            
            doc.autoTable({
                startY: 50,
                head: [['Kode QR', 'Barang', 'Jumlah', 'Warna', 'Order']],
                body: tableRows,
                theme: 'grid',
                headStyles: { 
                    fillColor: [22, 101, 52], // green-800
                    textColor: [255, 255, 255],
                    fontStyle: 'bold'
                },
                styles: {
                    fontSize: 9,
                    cellPadding: 3
                }
            });

            // Total
            const totalQty = cart.reduce((a, b) => a + b.qty, 0);
            const totalItems = cart.length;
            
            doc.autoTable({
                startY: doc.lastAutoTable.finalY + 10,
                head: [['Total Barang', 'Total Jumlah', 'Supplier']],
                body: [[totalItems, totalQty + ' Unit', supplier]],
                theme: 'grid',
                headStyles: { 
                    fillColor: [245, 245, 245],
                    textColor: [0, 0, 0],
                    fontStyle: 'bold'
                }
            });

            // Footer
            const finalY = doc.lastAutoTable.finalY + 20;
            doc.setFontSize(8);
            doc.text("Catatan: QR Code masing-masing produk tetap sama untuk setiap pemindaian.", 15, finalY);
            doc.text("Dokumen ini dicetak secara otomatis dari sistem QR Gudang 1 Maju Jaya Furniture.", 15, finalY + 5);

            // Simpan PDF
            doc.save(`SJ_Gudang1_${nota}.pdf`);
        }
    </script>

</body>

</html>
