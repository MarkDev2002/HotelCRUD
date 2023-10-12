<?php
include "./class/database.php";
class traphong extends database
{

    public function __construct()
    {
        parent::__construct();
    }

    // lấy thông tin hoá đơn
    public function getInvoiceDetails($mahd)
    {
        $sql = "SELECT h.mahd, h.tenhd, k.tenkh, t.ngaythue, t.ngaytra, h.tongtien, t.maphong, t.giathue
            FROM hoadon h
            INNER JOIN khachhang k ON h.makh = k.makh
            INNER JOIN thue t ON h.mahd = t.mahd
            WHERE h.mahd = '$mahd'";
        $result = $this->con->query($sql);
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    public function updateData($mahd, $ngaytraphongmoi)
    {
        // update ngaytraphong
        $sql_update_ngaytraphong_thue = "UPDATE thue
                                        SET ngaytra = '$ngaytraphongmoi'
                                        WHERE mahd = '$mahd'";

        //update tongtien
        $sql_update_tongtien_hoadon = "UPDATE hoadon h
            SET tongtien = TIMESTAMPDIFF(DAY, (SELECT ngaythue FROM thue t WHERE t.mahd = h.mahd), '$ngaytraphongmoi') * (SELECT giathue FROM thue t WHERE t.mahd = h.mahd) + 10000
            WHERE h.mahd = '$mahd'";

        //update tinhtrang phong
        $sql_update_phong = "UPDATE phong
                 SET tinhtrang = 'trống'
                 WHERE maphong IN (
                    SELECT maphong
                    FROM thue
                    WHERE mahd = '$mahd'
                )";

        if (
            $this->con->query($sql_update_ngaytraphong_thue) === TRUE &&
            $this->con->query($sql_update_tongtien_hoadon) === TRUE &&
            $this->con->query($sql_update_phong) === TRUE
        ) {
            return true;
        } else {
            echo "Lỗi khi cập nhật thông tin: " . $this->con->error;
            return false;
        }
    }
}

// Khởi tạo đối tượng traphong với kết nối cơ sở dữ liệu
$traphongManager = new traphong();

// Lấy thông tin hóa đơn từ URL
if (isset($_GET["mahd"])) {
    $mahd = $_GET["mahd"];
    $invoiceDetails = $traphongManager->getInvoiceDetails($mahd);

    if ($invoiceDetails !== null) {
        // Xử lý form submit
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $mahd = $_POST["mahd"];
            $ngaytraphongmoi = $_POST["ngaytraphongmoi"];

            // Cập nhật dữ liệu
            if ($traphongManager->updateData($mahd, $ngaytraphongmoi)) {
                header("Location: thongkethanhtoan.php?mahd=$mahd");
                exit();
            } else {
                echo "Lỗi khi cập nhật thông tin.";
            }
        }
    } else {
        echo "Không tìm thấy thông tin hóa đơn.";
    }
} else {
    echo "Thiếu thông tin hóa đơn trong URL.";
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Trả Phòng</title>
    <style>
        .form-box1 {
            position: relative;
            width: 400px;
            height: 500px;
            background: transparent;
            backdrop-filter: blur(15px);
            border: 2px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            display: flex;
            justify-content: center;
        }

        h1 {
            font-size: 40px;
            color: #fff;
            margin-left: 100px;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        p {
            font-size: 20px;
            padding: 5px;
        }
    </style>
    <link rel="stylesheet" href="./assets/index.css">
</head>

<body>
    <section>
        <div class="form-box1">
            <div>
                <h1>RECEIPT</h1>
                <div style="color:#fff">
                    <form action="traphong.php?mahd=<?php echo $mahd; ?>" method="POST">
                        <input type="hidden" name="mahd" value="<?php echo $mahd; ?>">
                        <p> <?php echo $invoiceDetails["tenhd"]; ?></p>
                        <p>Customer: <?php echo $invoiceDetails["tenkh"]; ?></p>
                        <p>Started date: <?php echo $invoiceDetails["ngaythue"]; ?></p>
                        <p>Antic check-out date: <?php echo $invoiceDetails["ngaytra"]; ?></p>
                        <p>Price: <?php echo $invoiceDetails["giathue"]; ?></p>
                        <label for="ngaytraphongmoi" style="font-size:20px;padding:5px">New check-out date:</label>
                        <input type="date" name="ngaytraphongmoi" id="ngaytraphongmoi" style="margin-left: 2px;background: transparent;border-radius: 20px;padding:5px;color: #fff;">
                        <p id="tongtien">Total cost: <?php echo $invoiceDetails["tongtien"]; ?></p>
                        <button type="submit" style="margin-top:65px">ACCEPT</button>
                    </form>
                </div>

                <script>
                    // Sự kiện khi ngày trả phòng dự kiến thay đổi
                    document.getElementById("ngaytraphongmoi").addEventListener("change", function() {
                        // Lấy giá trị mới của ngày trả phòng mới
                        var ngayTraPhongMoi = new Date(document.getElementById("ngaytraphongmoi").value);
                        var giaThue = <?php echo $invoiceDetails["giathue"]; ?>;
                        var ngayBatDauThue = new Date('<?php echo $invoiceDetails["ngaythue"]; ?>');
                        var ngayTraPhongDuKien = new Date('<?php echo $invoiceDetails["ngaytra"]; ?>');

                        var timeDiff = ngayTraPhongMoi - ngayBatDauThue;
                        var days = Math.ceil(timeDiff / (1000 * 3600 * 24));
                        var tongTien = days * giaThue + 10000;
                        document.getElementById("tongtien").innerHTML = "Tổng tiền: " + tongTien;
                    });
                </script>
            </div>
        </div>
    </section>
</body>

</html>