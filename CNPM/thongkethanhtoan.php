<?php
include "./class/database.php";

class thongkethanhtoan extends database
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getDetails($mahd)
    {
        $sql = "SELECT h.mahd, kh.tenkh, t.maphong, t.ngaythue, t.ngaytra, t.giathue, h.tongtien
                FROM hoadon h
                JOIN thue t ON h.mahd = t.mahd
                JOIN khachhang kh ON h.makh = kh.makh
                WHERE h.mahd = '$mahd'";

        $result = $this->con->query($sql);

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    public function deleteInvoice($mahd)
    {
        // Xoá thông tin khách hàng khỏi bảng thue sau khi thanh toán 
        $sql_delete_user_from_thue = "DELETE FROM thue WHERE mahd = '$mahd'";
        if ($this->con->query($sql_delete_user_from_thue) === TRUE) {
            // Xoá thông tin khách hàng khỏi bảng hoadon
            $sql_delete_user_from_hoadon = "DELETE FROM hoadon WHERE mahd = '$mahd'";
            if ($this->con->query($sql_delete_user_from_hoadon) === TRUE) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

// Khởi tạo đối tượng InvoiceManager với kết nối cơ sở dữ liệu
$invoiceManager = new thongkethanhtoan();

if (isset($_GET["mahd"])) {
    $mahd = $_GET["mahd"];

    // Lấy thông tin hóa đơn từ CSDL
    $rows = $invoiceManager->getDetails($mahd);
    if ($rows !== null) {
        $tenkh = $rows["tenkh"];
        $maphong = $rows["maphong"];
        $ngaythue = $rows["ngaythue"];
        $ngaytra = $rows["ngaytra"];
        $giathue = $rows["giathue"];
        $tongtien = $rows["tongtien"];

        // Tính số ngày thuê
        $ngayThueDate = new DateTime($ngaythue);
        $ngayTraDate = new DateTime($ngaytra);
        $soNgayThue = $ngayThueDate->diff($ngayTraDate)->days;
    } else {
        echo "Không tìm thấy thông tin hóa đơn.";
        exit();
    }
} else {
    echo "Thiếu thông tin hóa đơn trong URL.";
}

if (isset($_POST["delete_user"])) {
    if ($invoiceManager->deleteInvoice($mahd)) {
        echo "Xoá thông tin người dùng và hóa đơn thành công.";
        header("Location: thuephong.php"); // Quay trở lại trang thuê phòng sau khi xóa thông tin
        exit();
    } else {
        echo "Lỗi khi xóa thông tin người dùng và hóa đơn.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/index.css">
    <title>Bảng Kê Thanh Toán</title>
    <style>
        /* Thêm CSS cho bảng kê thanh toán (tuỳ chỉnh theo nhu cầu của bạn) */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            backdrop-filter: blur(15px);
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

    <h2 style=" padding:70px;font-size:40px">RECEIPT INFORMATION</h2>
    <table>
        <tr>
            <th>CUSTOMER</th>
            <th>ROOM</th>
            <th>CHECK-IN DATE</th>
            <th>CHECK-OUT DATE</th>
            <th>NUMBER OF STAYING'S DAY</th>
            <th>PRICE</th>
            <th>TOTAL COST(VNĐ)</th>
        </tr>
        <tr>
            <td><?php echo $tenkh; ?></td>
            <td><?php echo $maphong; ?></td>
            <td><?php echo $ngaythue; ?></td>
            <td><?php echo $ngaytra; ?></td>
            <td><?php echo $soNgayThue; ?></td>
            <td><?php echo $giathue; ?></td>
            <td><?php echo $tongtien; ?></td>
        </tr>
    </table>

    <form method="POST">
        <button type="submit" name="delete_user" style="width:600px;margin-top:150px;margin-left:450px">BACK TO RENTING DATABASE</button>
    </form>
</body>

</html>
