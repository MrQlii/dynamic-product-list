<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">📦 Daftar Produk</h2>
        
        <div class="overflow-x-auto rounded-lg shadow-md">
            <table class="w-full text-left border border-gray-300 bg-white">
                <thead class="bg-blue-500 text-white text-md">
                    <tr>
                        <th class="border p-3">No</th>
                        <th class="border p-3">Produk</th>
                        <th class="border p-3">Kategori</th>
                        <th class="border p-3">Gambar</th>
                        <th class="border p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="productTable"></tbody>
            </table>
        </div>
        <button id="addProduct" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700">
            <i class="fa-solid fa-plus-circle"></i> Tambah Produk
        </button>
        <p id="maxProductAlert" class="text-red-500 mt-2 hidden">⚠️ Anda sudah mencapai batas maksimum produk.</p>
    </div>  

    <div id="deleteImageModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <p class="text-lg font-bold">Apakah Anda yakin untuk menghapus gambar?</p>
            <div class="flex justify-center gap-4 mt-4">
                <button id="cancelDelete" class="px-4 py-2 rounded bg-gray-500 text-white">Batalkan</button>
                <button id="confirmDelete" class="px-4 py-2 rounded bg-red-600 text-white">Hapus</button>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            let productCount = 0;
            let targetDeleteImage = null;

            $("#addProduct").click(function() {
                if (productCount < 5) {
                    productCount++;
                    $("#maxProductAlert").addClass("hidden");

                    let row = `
                        <tr class="border product-row" data-id="${productCount}">
                            <td class="border p-2 text-center rowspan-cell" rowspan="1">${productCount}</td>
                            <td class="border p-2 rowspan-cell" rowspan="1">
                                <input type="text" class="w-full px-2 py-1 border rounded" placeholder="Nama Produk">
                            </td>
                            <td class="border p-2 category-cell">
                                <button class="addCategory px-2 py-1 mt-2 bg-green-500 text-white rounded hover:bg-green-700">
                                    <i class="fa-solid fa-plus-circle"></i> Kategori
                                </button>
                            </td>
                            <td class="border p-2 image-cell"></td>
                            <td class="border p-2 text-center rowspan-cell" rowspan="1">
                                <button class="deleteProduct px-2 py-1 text-white rounded hover:bg-red-700">
                                    ❌
                                </button>
                            </td>
                        </tr>
                    `;
                    $("#productTable").append(row);
                    if (productCount === 5) {
                        $("#addProduct").hide();
                        $("#maxProductAlert").removeClass("hidden");
                    }
                }
            });

            $(document).on("click", ".addCategory", function() {
                let productRow = $(this).closest("tr");
                let productId = productRow.attr("data-id");
                let categoryCount = $(`tr[data-product-id="${productId}"]`).length;

                if (categoryCount < 3) {
                    let newCategoryRow = `
                        <tr class="border extra-row ml-4 text-center" data-product-id="${productId}">
                            <td class="border p-2 category-cell">
                                <div class="category-item flex items-center gap-2">
                                    <input type="text" class="w-full px-2 py-1 border rounded" placeholder="Nama Kategori">
                                </div>
                            </td>
                            <td class="border p-2 image-cell">
                                <div class="image-item flex items-center justify-center gap-4">
                                    <div class="flex flex-col items-center">
                                        <input type="file" class="hidden uploadImage" accept="image/png, image/jpeg, image/jpg">
                                        <img src="https://via.placeholder.com/50" class="uploadedImage w-36 h-36 border rounded-lg shadow-md object-cover">
                                        <div class="flex justify-center gap-2 mt-2">
                                            <button class="uploadIcon px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-700">📤</button>
                                            <button class="deleteImage hidden px-2 py-1 bg-red-500 text-white rounded hover:bg-red-700">🗑</button>
                                        </div>
                                    </div>
                                    <button class="deleteCategory px-1 py-0.5 text-xs text-white rounded hover:bg-red-700 h-6 w-6 flex items-center justify-center">
                                        ❌
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;

                    productRow.after(newCategoryRow);
                    updateRowspan(productRow);

                    if (categoryCount + 1 === 3) {
                        productRow.find(".addCategory").hide();
                    }
                }
            });

            $(document).on("click", ".deleteCategory", function() {
                let categoryRow = $(this).closest("tr");
                let productId = categoryRow.attr("data-product-id");

                categoryRow.remove();
                updateRowspan($(`tr[data-id="${productId}"]`));

                let categoryCount = $(`tr[data-product-id="${productId}"]`).length;
                if (categoryCount < 3) {
                    $(`tr[data-id="${productId}"] .addCategory`).show();
                }
            });

            $(document).on("click", ".deleteProduct", function() {
                let productRow = $(this).closest("tr");
                let productId = productRow.attr("data-id");

                $(`tr[data-product-id="${productId}"]`).remove();
                productRow.remove();

                productCount--;
                if (productCount < 5) {
                    $("#addProduct").show();
                }

                $("#productTable .product-row").each((index, row) => {
                    $(row).find(".rowspan-cell").first().text(index + 1);
                });
            });

            function updateRowspan(productRow) {
                let productId = productRow.attr("data-id");
                let extraRows = $(`tr[data-product-id="${productId}"]`).length + 1;
                productRow.find(".rowspan-cell").attr("rowspan", extraRows);
            }

            $(document).on("click", ".uploadIcon", function() {
                $(this).closest(".image-item").find(".uploadImage").click();
            });

            $(document).on("change", ".uploadImage", function() {
                let file = this.files[0];
                let validExtensions = ["image/jpeg", "image/png", "image/jpg"];
                if (file && validExtensions.includes(file.type)) {
                    let reader = new FileReader();
                    reader.onload = (e) => {
                        let imgElement = $(this).closest(".image-item").find(".uploadedImage");
                        imgElement.attr("src", e.target.result);
                        $(this).closest(".image-item").find(".deleteImage").removeClass("hidden");
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert("Format file harus JPG, JPEG, atau PNG");
                }
            });

            $(document).on("click", ".deleteImage", function() {
                let imgContainer = $(this).closest(".image-item");
                imgContainer.find(".uploadedImage").attr("src", "https://via.placeholder.com/50");
                imgContainer.find(".deleteImage").addClass("hidden");
            });

            $(document).on("click", ".deleteImage", function() {
                targetDeleteImage = $(this).closest(".image-item");
                $("#deleteImageModal").removeClass("hidden");
            });

            $("#cancelDelete").click(function() {
                $("#deleteImageModal").addClass("hidden");
            });

            $("#confirmDelete").click(function() {
                if (targetDeleteImage) {
                    targetDeleteImage.find(".uploadedImage").attr("src", "https://via.placeholder.com/50");
                    targetDeleteImage.find(".deleteImage").addClass("hidden");

                    // targetDeleteImage.css("display", "none");
                    requestAnimationFrame(() => {
                        targetDeleteImage.find(".uploadedImage").attr("src", "");
                    });
                }
                $("#deleteImageModal").addClass("hidden");
            });

        });
    </script>
</body>
</html>
