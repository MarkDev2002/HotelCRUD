<?php
include "./class/database.php";
class chothue_save extends database {
    
    public function getRoomIdByName($tenphong) {
        $query = "SELECT maphong FROM phong WHERE tenphong = '$tenphong'";
        $result = mysqli_query($this->con, $query);
        if (!$result) {
            echo "Lỗi khi lấy mã phòng: " . mysqli_error($this->con);
            exit;
        }
        $row = mysqli_fetch_assoc($result);
        return $row['maphong'];
    }

    public function saveThue($mahd, $maphong, $check_in_date, $check_out_date, $price) {
        $query = "INSERT INTO thue (mahd, maphong, ngaythue, ngaytra, giathue) VALUES ('$mahd', '$maphong', '$check_in_date', '$check_out_date', '$price')";
        $result = mysqli_query($this->con, $query);
        if (!$result) {
            echo "Lỗi khi lưu thông tin thuê: " . mysqli_error($this->con);
            exit;
        }
    }

    public function activateRoom($maphong) {
        $query = "UPDATE phong SET tinhtrang = 'bận' WHERE maphong = '$maphong'";
        $result = mysqli_query($this->con, $query);
        if (!$result) {
            echo "Lỗi khi cập nhật tình trạng phòng: " . mysqli_error($this->con);
            exit;
        }
    }

    public function saveHoadon($mahd, $makh, $price) {
        $query = "INSERT INTO hoadon (mahd, tenhd, makh, tongtien) VALUES ('$mahd', '', '$makh', '$price')";
        $result = mysqli_query($this->con, $query);
        if (!$result) {
            echo "Lỗi khi lưu thông tin hóa đơn: " . mysqli_error($this->con);
            exit;
        }
    }
}

// Khởi tạo đối tượng RentalManager với kết nối cơ sở dữ liệu
$rentalManager = new chothue_save();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mahd = $_POST['Rcode'];
    $makh = $_POST['Rkh'];
    $price = $_POST['price'];
    $check_in_date = $_POST['Fday'];
    $check_out_date = $_POST['Tday'];
    $tenphong = $_POST['tenp'];

    $maphong = $rentalManager->getRoomIdByName($tenphong);
    
    $rentalManager->activateRoom($maphong);
    $rentalManager->saveHoadon($mahd, $makh, $price);
    $rentalManager->saveThue($mahd, $maphong, $check_in_date, $check_out_date, $price);

    // Chuyển hướng đến trang khác
    header('Location: thuephong.php');
    exit;
}

$conn->close();
?>
