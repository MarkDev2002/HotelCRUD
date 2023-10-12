<?php
include "./class/database.php";

class chothue extends database
{
    public function __construct()
    {
        parent::__construct();
        if ($this->con->connect_error) {
            die("Kết nối không thành công: " . $this->con->connect_error);
        }
    }

    public function getAvailableRooms()
    {
        $query = "SELECT tenphong FROM phong WHERE tinhtrang = 'trống'";
        $result = mysqli_query($this->con, $query);
        if (!$result) {
            echo "Không thể truy xuất dữ liệu " . mysqli_error($this->con);
            exit;
        }
        return $result;
    }
}

// Khởi tạo đối tượng với kết nối cơ sở dữ liệu
$reservationManager = new chothue();

$availableRooms = $reservationManager->getAvailableRooms();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="./assets/index.css">
    <title>HASH TECHIE OFFICIAL</title>
    <script src="https://kit.fontawesome.com/yourcode.js" crossorigin="anonymous"></script>
</head>

<body>

    <section>
        <div class="form-box">
            <div class="form-value">
                <form action="chothue_save.php" method="post">
                    <h2>RESERVATION SERVICE</h2>
                    <div class="inputbox">
                        <input name="Rcode" type="text" required>
                        <label for="">Bill code</label>
                    </div>
                    <div class="inputbox">
                        <input name="Rkh" type="text" required>
                        <label for="">Customer ID</label>
                    </div>
                    <div class="inputbox">
                        <input name="price" type="number" required>
                        <label for="">Price</label>
                    </div>
                    <div style="padding-top: 15px;padding-bottom: 15px;margin-bottom: 10px;">
                        <h7>Enter check-in date</h7>
                        <input type="date" name="Fday" value="<?php echo date('Y-m-d'); ?>" style="margin-left: 20px;background: transparent;border-radius: 20px;padding:5px;color: #fff;">
                    </div>
                    <div>
                        <h7>Enter check-out date</h7>
                        <input type="date" value="<?php
                                                    // Sử dụng strtotime để tính toán ngày tiếp theo (1 ngày sau)
                                                    $ngayTiepTheo = date("Y-m-d", strtotime("+1 day"));
                                                    echo $ngayTiepTheo;
                                                    ?>" name="Tday" style="margin-left: 10px;background: transparent;border-radius: 20px;padding:5px;color: #fff;">
                    </div>
                    <div style="padding-top: 30px;padding-left: 5px;color: #fff;">
                        <label>Choose available room</label>
                        <select name="tenp" style="margin-left: 10px;background: transparent;border-radius: 20px;padding:5px;color: #fff;">
                            <?php while ($query_row = mysqli_fetch_assoc($availableRooms)) { ?>
                                <option style="color: black;"><?php echo $query_row['tenphong']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div style="padding-top: 30px;">
                        <button type="submit">CONFIRM</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>