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
    <style>
        body { background-color: #f0fdf4; font-family: 'Inter', sans-serif; }
        .glass-card { background: white; border-radius: 2rem; border: 1px solid #dcfce7; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        select { cursor: pointer !important; z-index: 50; position: relative; }
        #qrcodeCanvas img { margin: 0 auto; border: 4px solid white; border-radius: 10px; }
        .qr-placeholder { 
            width: 120px; height: 120px; 
            display: flex; align-items: center; justify-content: center;
            background: #f9fafb; border: 2px dashed #d1d5db; border-radius: 10px;
        }
    </style>
</head>
<body class="p-4 md:p-10">

    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex justify-between items-center bg-white p-6 rounded-3xl shadow-sm border border-green-100">
            <div>
                <h1 class="text-2xl font-black text-green-800">MAJU JAYA <span class="text-green-500 font-light">FURNITURE</span></h1>
                <p class="text-[10px] text-gray-400 uppercase tracking-widest">Penerima: <span class="text-green-600 font-bold">Gudang 1</span></p>
            </div>
            <div id="notaDisplay" class="font-mono text-sm font-bold text-green-700 bg-green-50 px-4 py-2 rounded-xl border border-green-200"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="glass-card p-8 flex flex-col items-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-green-500"></div>
                <label class="text-[10px] font-black text-green-700 uppercase mb-4 tracking-widest">QR Scanner Ready (U2-B)</label>
                
                <input type="text" id="barcodeInput" autofocus 
                       class="w-full p-5 text-center text-2xl font-mono border-2 border-green-100 rounded-2xl focus:border-green-500 focus:ring-4 focus:ring-green-50 outline-none transition-all"
                       placeholder="SCAN QR DISINI">
                
                <div class="mt-6 p-4 bg-white rounded-2xl border border-gray-100 shadow-inner flex flex-col items-center">
                    <div id="qrcodeCanvas" class="min-h-[120px] min-w-[120px] flex items-center justify-center">
                        <div class="qr-placeholder">
                            <p class="text-[9px] text-gray-400 text-center px-2">QR akan tampil<br>setelah scan</p>
                        </div>
                    </div>
                    <p class="text-[9px] mt-2 text-gray-400 font-bold uppercase tracking-tighter">QR Code Produk</p>
                    <p id="currentProductId" class="text-[8px] mt-1 text-green-600 font-mono hidden"></p>
                </div>
            </div>

            <div class="glass-card p-8 flex flex-col justify-between">
                <div>
                    <label class="text-xs font-black text-gray-400 uppercase mb-2 block tracking-widest">Pilih Supplier Finishing</label>
                    <select id="supplierSelect" class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl font-bold text-gray-700 outline-none focus:border-green-500 transition-all hover:bg-gray-100">
                        <option value="Bpk. Bambang Pamungkas">Bpk. Bambang Pamungkas</option>
                        <option value="Bpk. Budi Setiawan">Bpk. Budi Setiawan</option>
                        <option value="Bpk. Agus Santoso">Bpk. Agus Santoso</option>
                        <option value="Bpk. Slamet Riyadi">Bpk. Slamet Riyadi</option>
                        <option value="Solonet">Solonet</option>
                    </select>
                </div>
                <div class="mt-4 p-4 bg-green-50 rounded-2xl border border-green-100">
                    <p class="text-[11px] text-green-800 leading-relaxed">
                        <strong>Status QR:</strong> Aktif. Setiap produk memiliki QR Code unik yang tetap sama. Gunakan alat pemindai untuk memasukkan kode.
                    </p>
                </div>
            </div>
        </div>

        <div class="glass-card overflow-hidden">
            <div class="p-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-black text-gray-700 uppercase tracking-widest text-sm">Cart List Product</h3>
                <span id="unitBadge" class="bg-green-600 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase">0 Unit</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-white">
                        <tr>
                            <th class="p-5">Kode QR</th>
                            <th class="p-5">Barang</th>
                            <th class="p-5 text-center">Jumlah</th>
                            <th class="p-5">Warna</th>
                            <th class="p-5">Order</th>
                            <th class="p-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cartTableBody" class="text-sm text-gray-700 font-medium"></tbody>
                </table>
                <div id="noDataRow" class="p-20 text-center text-gray-300 italic">Silahkan scan QR barang untuk Gudang 1...</div>
            </div>
        </div>

        <button onclick="exportToPDF()" class="w-full bg-green-700 hover:bg-green-800 text-white font-black py-6 rounded-3xl shadow-xl transition-all transform active:scale-95 uppercase tracking-[0.3em] text-lg">
            Simpan & Export PDF
        </button>
    </div>

    <script>
        // 1. DATABASE LENGKAP dengan QR Code tetap
        const db = [
            { 
                id: "TRX52879071", 
                qrCode: "TRX52879071",
                nama: "Buffet en bois d'acajou 160 LONDRES", 
                warna: "Mahogany Red", 
                order: "ORD-992", 
                ket: "Kualitas Ekspor" 
            },
            { 
                id: "10", 
                qrCode: "10",
                nama: "Buffet en bois d'acajou et teck 180 LONDRES", 
                warna: "Natural Teak", 
                order: "ORD-881", 
                ket: "Finishing Halus" 
            },
            { 
                id: "QR-15134002", 
                qrCode: "QR-15134002",
                nama: "Meja Makan Teak Minimalis Custom", 
                warna: "Dark Brown", 
                order: "ORD-775", 
                ket: "Solid Wood" 
            },
            { 
                id: "2024", 
                qrCode: "2024",
                nama: "Kursi Makan Minimalis Teak", 
                warna: "Dark Walnut", 
                order: "ORD-663", 
                ket: "Set of 4" 
            },
            { 
                id: "MJ-001", 
                qrCode: "MJ-001",
                nama: "Lemari Pakaian 2 Pintu", 
                warna: "White Oak", 
                order: "ORD-449", 
                ket: "Anti Rayap" 
            }
        ];

        let cart = [];
        const generatedNota = "NOTA-" + Math.floor(10000 + Math.random() * 90000);
        document.getElementById('notaDisplay').innerText = "NO. NOTA: " + generatedNota;

        // Cache untuk QR Code yang sudah dibuat
        const qrCache = new Map();

        // Fungsi untuk membuat atau mengambil QR Code dari cache
        function getOrCreateQRCode(productId, text) {
            if (qrCache.has(productId)) {
                return qrCache.get(productId);
            }
            
            const container = document.createElement('div');
            new QRCode(container, {
                text: text || productId,
                width: 120,
                height: 120,
                colorDark: "#166534",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
            
            qrCache.set(productId, container);
            return container;
        }

        // Fungsi untuk menampilkan QR Code produk
        function displayProductQR(product) {
            const qrContainer = document.getElementById('qrcodeCanvas');
            const productIdDisplay = document.getElementById('currentProductId');
            
            if (product) {
                const qrElement = getOrCreateQRCode(product.id, product.qrCode || product.id);
                qrContainer.innerHTML = '';
                qrContainer.appendChild(qrElement.cloneNode(true));
                productIdDisplay.textContent = product.id;
                productIdDisplay.classList.remove('hidden');
            } else {
                qrContainer.innerHTML = `
                    <div class="qr-placeholder">
                        <p class="text-[9px] text-gray-400 text-center px-2">QR akan tampil<br>setelah scan</p>
                    </div>
                `;
                productIdDisplay.textContent = '';
                productIdDisplay.classList.add('hidden');
            }
        }

        const input = document.getElementById('barcodeInput');
        const supplierSelect = document.getElementById('supplierSelect');

        // Auto-focus management
        document.addEventListener('click', (e) => {
            if (e.target !== supplierSelect) {
                input.focus();
            }
        });

        // Handle input scan
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const val = input.value.trim();
                
                // Cari produk berdasarkan ID atau QR Code
                let product = db.find(p => p.id === val || p.qrCode === val);
                
                if (product) {
                    // Cek apakah produk sudah ada di cart
                    const exist = cart.find(c => c.id === product.id);
                    if (exist) {
                        exist.qty++;
                    } else {
                        cart.push({ 
                            ...product, 
                            qty: 1 
                        });
                    }
                    
                    // Tampilkan QR Code produk yang discan
                    displayProductQR(product);
                    renderTable();
                    
                    // Notifikasi sukses
                    input.classList.add('border-green-500', 'bg-green-50');
                    setTimeout(() => {
                        input.classList.remove('border-green-500', 'bg-green-50');
                    }, 500);
                    
                } else {
                    // Notifikasi error
                    input.classList.add('border-red-500', 'bg-red-50');
                    setTimeout(() => {
                        input.classList.remove('border-red-500', 'bg-red-50');
                    }, 1000);
                    
                    // Tampilkan QR Code kosong jika produk tidak dikenal
                    displayProductQR(null);
                }
                
                input.value = '';
                input.focus();
            }
        });

        // Render table
        function renderTable() {
            const body = document.getElementById('cartTableBody');
            const placeholder = document.getElementById('noDataRow');
            const badge = document.getElementById('unitBadge');

            if (cart.length > 0) {
                placeholder.classList.add('hidden');
                body.innerHTML = cart.map((c, i) => `
                    <tr class="border-b border-gray-50 hover:bg-green-50/50">
                        <td class="p-5 font-bold text-green-700 font-mono italic">${c.id}</td>
                        <td class="p-5">${c.nama}</td>
                        <td class="p-5 text-center font-black">${c.qty}</td>
                        <td class="p-5 text-gray-400 italic">${c.warna}</td>
                        <td class="p-5 font-mono text-xs text-blue-500">${c.order}</td>
                        <td class="p-5 text-right">
                            <button onclick="removeItem(${i})" 
                                    class="text-red-300 hover:text-red-500 font-bold text-sm px-3 py-1 rounded-lg hover:bg-red-50 transition-colors">
                                Hapus
                            </button>
                        </td>
                    </tr>
                `).join('');
            } else {
                body.innerHTML = '';
                placeholder.classList.remove('hidden');
            }
            
            const totalQty = cart.reduce((a, b) => a + b.qty, 0);
            badge.innerText = totalQty + " UNIT";
            badge.className = totalQty > 0 ? 
                "bg-green-600 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase" :
                "bg-gray-400 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase";
        }

        // Hapus item dari cart
        function removeItem(index) {
            cart.splice(index, 1);
            
            // Jika cart kosong, tampilkan placeholder QR
            if (cart.length === 0) {
                displayProductQR(null);
            } else {
                // Tampilkan QR Code dari item pertama di cart
                displayProductQR(cart[0]);
            }
            
            renderTable();
        }

        // Export PDF
        function exportToPDF() {
            if (cart.length === 0) {
                alert("Silahkan scan QR barang terlebih dahulu!");
                return;
            }
            
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const supplier = supplierSelect.value;
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
            doc.text(`No. Nota: ${generatedNota}`, 140, 35);
            doc.text(`Tanggal: ${date}`, 140, 40);

            // Tabel barang
            const tableRows = cart.map(item => [
                item.id,
                item.nama,
                item.qty,
                item.warna,
                item.order,
                item.ket
            ]);
            
            doc.autoTable({
                startY: 50,
                head: [['Kode QR', 'Barang', 'Jumlah', 'Warna', 'Order', 'Keterangan']],
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
            doc.save(`SJ_Gudang1_${generatedNota}.pdf`);
        }

        // Inisialisasi QR Code untuk produk contoh di cache
        window.addEventListener('DOMContentLoaded', () => {
            // Pre-generate QR Codes untuk produk di database
            db.forEach(product => {
                getOrCreateQRCode(product.id, product.qrCode || product.id);
            });
            
            // Auto-focus input
            input.focus();
        });
    </script>
</body>
</html>