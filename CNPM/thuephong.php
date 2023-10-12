<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Danh Sách Thuê Phòng</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="./assets/index.css" type="text/css" />
  <style>
    .box-7a {
    margin-left: 285px;
    padding-bottom: 20px;
    width: 100%;
    background: transparent;
    backdrop-filter: blur(15px);
    height: 450px;
    border-radius: 30px;
}
  </style>
</head>

<body>
  <?php
  include "./class/database.php";

  class thuephong extends database
  {
    public function __construct()
    {
      parent::__construct();
    }

    // Lấy dữ liệu hoá đơn
    public function getRentalData()
    {
      $query = "SELECT T.mahd, T.maphong, T.ngaythue, T.ngaytra, hd.mahd, hd.makh, kh.tenkh FROM thue AS T, hoadon AS hd, khachhang AS kh WHERE T.mahd = hd.mahd AND hd.makh = kh.makh";
      $result = mysqli_query($this->con, $query);
      if (!$result) {
        echo "Không thể truy xuất dữ liệu " . mysqli_error($this->con);
        exit;
      }
      return $result;
    }
  }


  // Khởi tạo đối tượng thuephong với kết nối cơ sở dữ liệu
  $thuePhongManager = new thuephong();

  $result = $thuePhongManager->getRentalData();
  ?>

  <div id="boxtb" style="
    background-color: white;
    height: -100px;
    width: 500px;
    position: fixed;
    z-index: 10;
    right: 20%;
    display: flex;
">
  </div>
  <div id="shows">
    <div id="main">
      <div class="head">
        <div class="col-div-6">
          <span onclick="invinbox()" style="font-size: 30px; cursor: pointer; color: white" class="nav2">&#9776;MANAGING ROOM</span>
        </div>
        <a href="chothue.php">
          <h3 style="color: #f1f2f6;clear: both;font-size: 30px;padding-top: 30px;">RENTING ROOM</h3>
        </a>
      </div>
    </div>
    <div class="col-div-8">
      <div class="box-7a">
        <div class="content-box">
          <input style="
                    width: 95%;
                    height: 40px;
                    font-size: 15px;
                    padding-left: 20px;
                    border-radius:20px;
                " type="text" id="search-box" placeholder="searching..." onkeyup="filterOf()" />

          <table id="showlist">
            <tr>
              <th>ID_RECEIPT</th>
              <th>CUSTOMER</th>
              <th>ROOM</th>
              <th>Check-in</th>
              <th>Check-out</th>
              <th>ACTION</th>
            </tr>
            <?php
            while ($query_row = mysqli_fetch_assoc($result)) { ?>
              <tr>
                <th><?php echo $query_row['mahd'] ?></th>
                <th><?php echo $query_row['tenkh'] ?></th>
                <th><?php echo $query_row['maphong'] ?></th>
                <th><?php echo $query_row['ngaythue'] ?></th>
                <th><?php echo $query_row['ngaytra'] ?></th>
                <th><a href="traphong.php?mahd=<?php echo $query_row['mahd']; ?>">CHECK-OUT</a></th>
              </tr>
            <?php } ?>
          </table>
        </div>
      </div>
    </div>
  </div>
</body>

</html>