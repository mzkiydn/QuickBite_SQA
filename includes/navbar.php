<?php
?>
<link rel="stylesheet" href="../../assets/css/styles.css">
<div id="side-nav" class="side-nav">
    <a href="javascript:void(0)" class="close-btn" onclick="toggleNav()">&times;</a>
    <a href="../User/index.php">User</a>
    <a href="../Order/order.php">Order</a>
    <a href="../Menu/menu.php">Menu</a>
    <a href="../Payment/payment.php">Payment</a>
</div>

<style>
    /* Side Navigation Bar */
    .side-nav {
        height: 100%;
        width: 0;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #111;
        overflow-x: hidden;
        transition: 0.5s;
        padding-top: 60px;
    }

    .side-nav a {
        padding: 10px 15px;
        text-decoration: none;
        font-size: 18px;
        color: #fff;
        display: block;
        transition: 0.3s;
    }

    .side-nav a:hover {
        background-color: #575757;
    }

    .side-nav .close-btn {
        position: absolute;
        top: 0;
        right: 15px;
        font-size: 36px;
        color: #fff;
    }
</style>