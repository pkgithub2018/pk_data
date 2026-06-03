 <div align="center" class="cominpt" style="width: 100%; display: flex; flex-direction: row; align-items: flex-start; justify-content: center;"> <!-- Main content -->	

    <div class="dv-left" style="width: 40%; justify-content: flex-start;"> <!-- LEFT SIDE -->
        <?php
            // Include database connection
            $utype = $_SESSION["usertype"];
            
            // --- Month and Year filter dropdowns ---
            $selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');
            $selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');


            // Get the student ID from the request
            echo "<h2>ລາຍງານການຊໍາລະເງິນ</h2>";
                // --- Month filter dropdown ---
            $selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');
            echo "<form method='get' action='content.php' style='margin-bottom: 15px;'>";
            echo "<input type='hidden' name='sad' value='schfeesreport'>";
            echo "<input type='hidden' name='sadkid' value='kidfeesreport'>";
            echo "<div style='display: flex; align-items: center; gap: 15px; margin-left: 30px; margin-bottom: 25px;'>";
            echo "<label for='month' style='font-weight:bold;'>ເລືອກເດືອນ: </label>";
            echo "<select name='month' id='month' onchange='this.form.submit()' style='width: 120px; height: 40px;'>";
           
                $val = str_pad($m, 2, '0', STR_PAD_LEFT);
                $lao_months = [
                    '01'=>'ມັງກອນ', '02'=>'ກຸມພາ', '03'=>'ມີນາ', '04'=>'ເມສາ', '05'=>'ພຶດສະພາ', '06'=>'ມິຖຸນາ',
                    '07'=>'ກໍລະກົດ', '08'=>'ສິງຫາ', '09'=>'ກັນຍາ', '10'=>'ຕຸລາ', '11'=>'ພະຈິກ', '12'=>'ທັນວາ'
                ];
                 for ($m = 1; $m <= 12; $m++) {
                    $val = str_pad($m, 2, '0', STR_PAD_LEFT);
                    $selected = ($val == $selected_month) ? "selected" : "";
                    echo "<option value='$val' $selected>{$lao_months[$val]} ($val)</option>";
                 }
            echo "</select>";

            // Year dropdown (current year and previous year)
            $current_year = date('Y');
            $years = [$current_year - 1, $current_year];
            echo "&nbsp;&nbsp;<label for='year' style='font-weight:bold;'>ປີ: </label>";
            echo "<select name='year' id='year' onchange='this.form.submit()' style='width: 120px; height: 40px;'>";
            foreach ($years as $year) {
                $selected = ($year == $selected_year) ? "selected" : "";
                echo "<option value='$year' $selected>$year</option>";
            }
            echo "</select>";
            echo "</div>";
            echo "</form>";

            echo "<table style='width: 90%; border-collapse: collapse;'>";
            echo "<tr><th>ວັນທີ</th><th>ລາຍການ</th><th>ຈຳນວນ</th><th>ລາຍລະອຽດ</th></tr>";
                    PaymentListDate($utype, $selected_month, $selected_year, $con);
            echo "</table>";
        ?>
    </div> <!-- End of LEFT SIDE -->
    <div class="dv-right" style="width: 60%; margin-top: 70px; margin-left: 20px; margin-right: 20px;"> <!-- RIGHT SIDE -->
        <?php
      
        // Get paydate from the request
        $paydate = isset($_GET['paydate']) ? $_GET['paydate'] : '';
        PaymentDetails($paydate, $con);

        ?>
    </div> <!-- End of RIGHT SIDE -->
</div>