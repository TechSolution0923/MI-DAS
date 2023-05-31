<tr>
    <td><b><?php echo $year0?></b></td>
    <?php echo $quantityyear0table ?>
</tr>
<tr class="">
    <td><div style="margin-top: -4px;">Target</div></td>
    <?php
    for ($i = 1; $i <= 12; $i++)
    {
        $pre='';
        if ($i < 10) {
            $pre='0';
        }
        if ($salesTargetForLastThreeYear['monthlysalespc'][$year0.$pre.$i] < $G_MarginOk) $class0="bg-red-full";
        if ($salesTargetForLastThreeYear['monthlysalespc'][$year0.$pre.$i] >= $G_MarginOk AND $salesTargetForLastThreeYear['monthlysalespc'][$year0.$pre.$i] < $G_MarginGood) $class0="bg-yellow-full";
        if ($salesTargetForLastThreeYear['monthlysalespc'][$year0.$pre.$i] >= $G_MarginGood) $class0="bg-green-full";
        if (empty($salesTargetForLastThreeYear[$year0.$pre.$i])) $class0="bg-green-full";
        if ($i > date('n',time())) { $class0=""; }
        ?>
        <td>
<!--										<div style="width:100%;height:5px !important;margin-top: -8px;" class="--><?php //echo $class0; ?><!--">&nbsp</div>-->
            <?php
            // if (isset($salesTargetForLastThreeYear[$year0.$pre.$i])) {
            //     echo number_format($salesTargetForLastThreeYear[$year0.$pre.$i]);
            // } else {
                echo '0';
            // }
            ?>
        </td>
        <?php
    }
    ?>
    <td>
        <?php
        // if (isset($salesTargetForLastThreeYear[$year0])){
        //     $totalTar0=array_sum($salesTargetForLastThreeYear[$year0]);
        //     $totalmonthlysalespc0 = ($quantityyear0total / $totalTar0) * 100;
        // }
        // if ($totalmonthlysalespc0 < $G_kpithreshold1) $classt0="bg-red";
        // if ($totalmonthlysalespc0 >= $G_kpithreshold1 AND $stotalmonthlysalespc0 < $G_kpithreshold2) $classt0="bg-yellow";
        // if ($totalmonthlysalespc0 >= $G_kpithreshold2) $classt0="bg-green";
        // if (empty($salesTargetForLastThreeYear[$year0])) $classt0="bg-green";
        ?>

        <!-- <?php echo (isset($salesTargetForLastThreeYear[$year0])?number_format(array_sum($salesTargetForLastThreeYear[$year0])):'0'); ?> -->
        <?php echo '0' ?>
    </td>
</tr>
<tr class="border-target">
    <td>%</td>
    <?php
    $quantityyear0data = ltrim($quantityyear0data, '[');
    $quantityyear0data = rtrim($quantityyear0data, ']');
    $quantityyear0data = explode(',', $quantityyear0data);
    for ($i = 1; $i <= 12; $i++)
    {
        $pre = '';
        if ($i < 10)
        {
            $pre = '0';
        }
        if ($yearstartmonth > date('m') && date('m') < $i && $i < $yearstartmonth) {
            echo '<td></td>';
        } else if ($yearstartmonth <= date('m') && ($i < $yearstartmonth || date('m') < $i)) {
            echo '<td></td>';
        }
        // else if (isset($salesTargetForLastThreeYear[$year0.$pre.$i])) {
        //     $percentage = ($quantityyear0data[$i-1] / $salesTargetForLastThreeYear[$year0.$pre.$i]) * 100;
        //     $colourPercentage = $percentage;
        //     (float)$percentage -= 100;
        //     $percentagePrint = number_format($percentage);
        //     if ($colourPercentage < (float)$G_kpithreshold1)
        //         $class0 = "bg-red-full";
        //     if ($colourPercentage >= (float)$G_kpithreshold1 && $colourPercentage < (float)$G_kpithreshold2)
        //         $class0 = "bg-yellow-full";
        //     if ($colourPercentage >= (float)$G_kpithreshold2)
        //         $class0 = "bg-green-full";
        //     if (empty($colourPercentage))
        //         $class0 = "bg-green-full";
        //     echo '<td class="'.$class0.'">'.$percentagePrint.'%</td>';
        // }
        else {
            echo '<td class="bg-green-full">0%</td>';
        }
    }
    // if (isset($salesTargetForLastThreeYear[$year0]))
    // {
    //     $totalSales0 = array_sum($quantityyear0data);
    //     $totalTarget0 = array_sum($salesTargetForLastThreeYear[$year0]);
    //     $totalPercentage = ($totalSales0 / $totalTarget0) * 100;
    //     $colourPercentage = $totalPercentage;
    //     (float)$totalPercentage -= 100;
    //     $totalPercentagePrint = number_format($totalPercentage);
    //     if ($colourPercentage < (float)$G_kpithreshold1)
    //         $class0 = "bg-red-full";
    //     if ($colourPercentage >= (float)$G_kpithreshold1 && $colourPercentage < (float)$G_kpithreshold2)
    //         $class0 = "bg-yellow-full";
    //     if ($colourPercentage >= (float)$G_kpithreshold2)
    //         $class0 = "bg-green-full";
    //     if (empty($colourPercentage))
    //         $class0 = "bg-green-full";
    //     echo '<td class="'.$class0.'">' . number_format($totalPercentagePrint) . '%</td>';
    // }
    // else
        echo '<td class="bg-green-full">0%</td>';
    ?>
</tr>
<tr>
    <td><b><?php echo $year0?> Cml.</b></td>
    <?php
        $runningTotal = 0;
        foreach ($quantityyear0data as $data)
        {
            $runningTotal += $data;
            echo '<td><b>'.number_format($runningTotal).'</b></td>';
        }
        echo '<td><b>'.number_format(array_sum($quantityyear0data)).'</b></td>';
    ?>
</tr>
<tr class="">
    <td><div style="margin-top: -4px;">Target</div></td>
    <?php
    $runningTotalTarget = 0;
    for ($i = 1; $i <= 12; $i++)
    {
        $pre='';
        if ($i < 10) {
            $pre='0';
        }
        if ($salesTargetForLastThreeYear['monthlysalespc'][$year1.$pre.$i] < $G_kpithreshold1) $class1="bg-red";
        if ($salesTargetForLastThreeYear['monthlysalespc'][$year1.$pre.$i] >= $G_kpithreshold1 AND $salesTargetForLastThreeYear['monthlysalespc'][$year1.$pre.$i] < $G_kpithreshold2) $class1="bg-yellow";
        if ($salesTargetForLastThreeYear['monthlysalespc'][$year1.$pre.$i] >= $G_kpithreshold2) $class1="bg-green";
        if (empty($salesTargetForLastThreeYear[$year1.$pre.$i])) $class1="bg-green";
        ?>
        <td>
<!--										<div style="width:100%;height:5px !important;margin-top: -8px;" class="--><?php //echo $class1; ?><!--">&nbsp</div>-->
            <?php
                // $runningTotalTarget += $salesTargetForLastThreeYear[$year0.$pre.$i];
                // echo number_format($runningTotalTarget);
                echo '0';
            ?>
        </td>
        <?php
    }
    ?>
    <td>
        <?php
        // if (isset($salesTargetForLastThreeYear[$year0])){
        //     $totalTar1=array_sum($salesTargetForLastThreeYear[$year0]);
        //     $totalmonthlysalespc0 = ($quantityyear0total / $totalTar0) * 100;
        // }
        // if ($totalmonthlysalespc0 < $G_kpithreshold1) $classt1="bg-red";
        // if ($totalmonthlysalespc0 >= $G_kpithreshold1 AND $stotalmonthlysalespc0 < $G_kpithreshold2) $classt1="bg-yellow";
        // if ($totalmonthlysalespc0 >= $G_kpithreshold2) $classt1="bg-green";
        // if (empty($salesTargetForLastThreeYear[$year0])) $classt1="bg-green";
        ?>
        <!-- <?php echo (isset($salesTargetForLastThreeYear[$year0])?number_format(array_sum($salesTargetForLastThreeYear[$year0])):'0'); ?> -->
        <?php echo '0' ?>
    </td>
</tr>
<tr class="border-target">
    <td>%</td>
    <?php
    $quantityyear1data = ltrim($quantityyear1data, '[');
    $quantityyear1data = rtrim($quantityyear1data, ']');
    $quantityyear1data = explode(',', $quantityyear1data);
    $runningTotal = 0;
    $runningTotalTarget = 0;
    for ($i = 1; $i <= 12; $i++)
    {
        // $percentage = 0;
        // $colourPercentage = 0;
        // $pre = '';
        // if ($i < 10)
        // {
        //     $pre = '0';
        // }
        // $runningTotal += $quantityyear0data[$i-1];
        // $runningTotalTarget += $salesTargetForLastThreeYear[$year0.$pre.$i];
        // if ($runningTotalTarget > 0 && $runningTotal > 0)
        // {
        //     $percentage = ($runningTotal / $runningTotalTarget) * 100;
        //     (float)$colourPercentage = $percentage;
        //     (float)$percentage -= 100;
        // }
        // $percentagePrint = number_format($percentage);
        // if ($colourPercentage < (float)$G_kpithreshold1)
        //     $class0 = "bg-red-full";
        // if ($colourPercentage >= (float)$G_kpithreshold1 && $colourPercentage < (float)$G_kpithreshold2)
        //     $class0 = "bg-yellow-full";
        // if ($colourPercentage >= (float)$G_kpithreshold2)
        //     $class0 = "bg-green-full";
        // if (empty($colourPercentage))
        //     $class0 = "bg-green-full";
        // echo '<td class="'.$class0.'">'.$percentagePrint.'%</td>';
        echo '<td class="bg-green-full">0%</td>';
    }
    // if (isset($salesTargetForLastThreeYear[$year0]))
    // {
    //     $totalSales0 = array_sum($quantityyear0data);
    //     $totalTarget0 = array_sum($salesTargetForLastThreeYear[$year0]);
    //     $totalPercentage = ($totalSales0 / $totalTarget0) * 100;
    //     (float)$colourPercentage = $totalPercentage;
    //     (float)$totalPercentage -= 100;
    //     $totalPercentagePrint = number_format($totalPercentage);
    //     if ($colourPercentage < (float)$G_kpithreshold1)
    //         $class0 = "bg-red-full";
    //     if ($colourPercentage >= (float)$G_kpithreshold1 && $colourPercentage < (float)$G_kpithreshold2)
    //         $class0 = "bg-yellow-full";
    //     if ($colourPercentage >= (float)$G_kpithreshold2)
    //         $class0 = "bg-green-full";
    //     if (empty($colourPercentage))
    //         $class0 = "bg-green-full";
    //     echo '<td class="'.$class0.'">' . $totalPercentagePrint . '%</td>';
    // }
    // else
        echo '<td class="bg-green-full">0%</td>';
    ?>
</tr>
<tr>
    <td><b><?php echo $year0?></b></td>
    <?php echo $quantityyear0table ?>
</tr>
<tr>
    <td><b><?php echo $year1?></b></td>
    <?php echo $quantityyear1table ?>
</tr>
<tr>
    <td><div style="margin-top: -4px;">%</div></td>
<?php
    for ($i = 0; $i < 12; $i++)
    {
        if ($yearstartmonth > date('m') && date('m') <= $i && $i < $yearstartmonth - 1) {
            echo '<td></td>';
        } else if ($yearstartmonth <= date('m') && ($i < $yearstartmonth - 1 || date('m') <= $i)) {
            echo '<td></td>';
        } else {
            $percentage = 0;
            if ($quantityyear1data[$i] != 0)
            {
                $percentage = ($quantityyear0data[$i] / $quantityyear1data[$i]) * 100;
            }

            $colourPercentage = $percentage;
            (float)$percentage -= 100;

            if ($colourPercentage < (float)$G_kpithreshold1)
            {
                $class0 = "red-arrow fa fa-arrow-down";
            }

            if ($colourPercentage >= (float)$G_kpithreshold1 && $colourPercentage < (float)$G_kpithreshold2)
            {
                $class0 = "yellow-arrow fa fa-arrow-right";
            }

            if ($colourPercentage >= (float)$G_kpithreshold2)
            {
                $class0 = "green-arrow fa fa-arrow-up";
            }

            if (empty($colourPercentage))
            {
                $class0 = "green-arrow fa fa-arrow-up";
            }
?>
            <td><?php echo number_format($percentage); ?>%<i class="<?php echo $class0; ?>"></i></td>
<?php
        }
    }

    if (isset($salesTargetForLastThreeYear[$year2]))
    {
        $totalTar2 = array_sum($salesTargetForLastThreeYear[$year2]);
        $totalmonthlysalespc2 = ($year2total / $totalTar2) * 100;
    }

    if ($totalmonthlysalespc2 < $G_kpithreshold2)
    {
        $classt2 = "bg-red";
    }

    if ($totalmonthlysalespc2 >= $G_kpithreshold2 AND $stotalmonthlysalespc2 < $G_kpithreshold2)
    {
        $classt2 = "bg-yellow";
    }

    if ($totalmonthlysalespc2 >= $G_kpithreshold2)
    {
        $classt2 = "bg-green";
    }

    if (empty($salesTargetForLastThreeYear[$year2]))
    {
        $classt2 = "bg-green";
    }

    $percentage = 0;
    if (array_sum($quantityyear1data) != 0)
    {
        $percentage = (array_sum($quantityyear0data) / array_sum($quantityyear1data)) * 100;
    }

    $colourPercentage = $percentage;
    (float)$percentage -= 100;

    if ($colourPercentage < (float)$G_kpithreshold1)
    {
        $class0 = "red-arrow fa fa-arrow-down";
    }

    if ($colourPercentage >= (float)$G_kpithreshold1 && $colourPercentage < (float)$G_kpithreshold2)
    {
        $class0 = "yellow-arrow fa fa-arrow-right";
    }

    if ($colourPercentage >= (float)$G_kpithreshold2)
    {
        $class0 = "green-arrow fa fa-arrow-up";
    }

    if (empty($colourPercentage))
    {
        $class0 = "green-arrow fa fa-arrow-up";
    }

    echo '<td class="">'.number_format($percentage).'%<i class="'.$class0.'"></i></td>';
?>
</tr>
<tr>
    <td><b><?php echo $year2?></b></td>
    <?php echo $quantityyear2table ?>
</tr>
<tr class="border-target">
    <td><div style="margin-top: -4px;">%</div></td>
<?php
    for ($i = 0; $i < 12; $i++)
    {
        if ($yearstartmonth > date('m') && date('m') <= $i && $i < $yearstartmonth - 1) {
            echo '<td></td>';
        } else if ($yearstartmonth <= date('m') && ($i < $yearstartmonth - 1 || date('m') <= $i)) {
            echo '<td></td>';
        } else {
            $percentage = 0;
            if ($quantityyear2data[$i] != 0)
            {
                $percentage = ($quantityyear0data[$i] / $quantityyear2data[$i]) * 100;
            }

            $colourPercentage = $percentage;
            (float)$percentage -= 100;

            if ($colourPercentage < (float)$G_kpithreshold1)
            {
                $class0 = "red-arrow fa fa-arrow-down";
            }

            if ($colourPercentage >= (float)$G_kpithreshold1 && $colourPercentage < (float)$G_kpithreshold2)
            {
                $class0 = "yellow-arrow fa fa-arrow-right";
            }

            if ($colourPercentage >= (float)$G_kpithreshold2)
            {
                $class0 = "green-arrow fa fa-arrow-up";
            }

            if (empty($colourPercentage))
            {
                $class0 = "green-arrow fa fa-arrow-up";
            }
?>
            <td><?php echo number_format($percentage); ?>%<i class="<?php echo $class0; ?>"></i></td>
<?php
        }
    }

    if (isset($salesTargetForLastThreeYear[$year2]))
    {
        $totalTar2 = array_sum($salesTargetForLastThreeYear[$year2]);
        $totalmonthlysalespc2 = ($year2total / $totalTar2) * 100;
    }

    if ($totalmonthlysalespc2 < $G_kpithreshold2)
    {
        $classt2 = "bg-red";
    }

    if ($totalmonthlysalespc2 >= $G_kpithreshold2 AND $stotalmonthlysalespc2 < $G_kpithreshold2)
    {
        $classt2 = "bg-yellow";
    }

    if ($totalmonthlysalespc2 >= $G_kpithreshold2)
    {
        $classt2 = "bg-green";
    }

    if (empty($salesTargetForLastThreeYear[$year2]))
    {
        $classt2 = "bg-green";
    }

    $percentage = 0;
    if (array_sum($quantityyear2data) != 0)
    {
        $percentage = (array_sum($quantityyear0data) / array_sum($quantityyear2data)) * 100;
    }

    $colourPercentage = $percentage;
    (float)$percentage -= 100;

    if ($colourPercentage < (float)$G_kpithreshold1)
    {
        $class0 = "red-arrow fa fa-arrow-down";
    }

    if ($colourPercentage >= (float)$G_kpithreshold1 && $colourPercentage < (float)$G_kpithreshold2)
    {
        $class0 = "yellow-arrow fa fa-arrow-right";
    }

    if ($colourPercentage >= (float)$G_kpithreshold2)
    {
        $class0 = "green-arrow fa fa-arrow-up";
    }

    if (empty($colourPercentage))
    {
        $class0 = "green-arrow fa fa-arrow-up";
    }

    echo '<td class="">'.number_format($percentage).'%<i class="'.$class0.'"></i></td>';
?>
</tr>
<tr>
    <td><b><?php echo $year0?> Cml.</b></td>
    <?php
        $runningTotal = 0;
        foreach ($quantityyear0data as $data)
        {
            $runningTotal += $data;
            echo '<td><b>'.number_format($runningTotal).'</b></td>';
        }
        echo '<td><b>'.number_format(array_sum($quantityyear0data)).'</b></td>';
    ?>
</tr>
<tr>
    <td><b><?php echo $year1?> Cml.</b></td>
    <?php
        $runningTotal = 0;
        foreach ($quantityyear1data as $data)
        {
            $runningTotal += $data;
            echo '<td><b>'.number_format($runningTotal).'</b></td>';
        }
        echo '<td><b>'.number_format(array_sum($quantityyear1data)).'</b></td>';
    ?>
</tr>
<tr>
    <td><div style="margin-top: -4px;">%</div></td>
<?php
    $runningTotalYear0 = 0;
    $runningTotalYear1 = 0;
    for ($i = 0; $i < 12; $i++)
    {
        $runningTotalYear0 += $quantityyear0data[$i];
        $runningTotalYear1 += $quantityyear1data[$i];
        
        $percentage = 0;
        if ($runningTotalYear1 != 0)
        {
            $percentage = ($runningTotalYear0 / $runningTotalYear1) * 100;
        }

        $colourPercentage = $percentage;
        (float)$percentage -= 100;

        if ($colourPercentage < (float)$G_kpithreshold1)
        {
            $class0 = "red-arrow fa fa-arrow-down";
        }

        if ($colourPercentage >= (float)$G_kpithreshold1 && $colourPercentage < (float)$G_kpithreshold2)
        {
            $class0 = "yellow-arrow fa fa-arrow-right";
        }

        if ($colourPercentage >= (float)$G_kpithreshold2)
        {
            $class0 = "green-arrow fa fa-arrow-up";
        }

        if (empty($colourPercentage))
        {
            $class0 = "green-arrow fa fa-arrow-up";
        }
?>
        <td><?php echo number_format($percentage); ?>%<i class="<?php echo $class0; ?>"></i></td>
<?php

    }

    if (isset($salesTargetForLastThreeYear[$year2]))
    {
        $totalTar2 = array_sum($salesTargetForLastThreeYear[$year2]);
        $totalmonthlysalespc2 = ($year2total / $totalTar2) * 100;
    }

    if ($totalmonthlysalespc2 < $G_kpithreshold2)
    {
        $classt2 = "bg-red";
    }

    if ($totalmonthlysalespc2 >= $G_kpithreshold2 AND $stotalmonthlysalespc2 < $G_kpithreshold2)
    {
        $classt2 = "bg-yellow";
    }

    if ($totalmonthlysalespc2 >= $G_kpithreshold2)
    {
        $classt2 = "bg-green";
    }

    if (empty($salesTargetForLastThreeYear[$year2]))
    {
        $classt2 = "bg-green";
    }

    $percentage = 0;
    if ($runningTotalYear1 != 0)
    {
        $percentage = ($runningTotalYear0 / $runningTotalYear1) * 100;
    }

    $colourPercentage = $percentage;
    (float)$percentage -= 100;

    if ($colourPercentage < (float)$G_kpithreshold1)
    {
        $class0 = "red-arrow fa fa-arrow-down";
    }

    if ($colourPercentage >= (float)$G_kpithreshold1 && $colourPercentage < (float)$G_kpithreshold2)
    {
        $class0 = "yellow-arrow fa fa-arrow-right";
    }

    if ($colourPercentage >= (float)$G_kpithreshold2)
    {
        $class0 = "green-arrow fa fa-arrow-up";
    }

    if (empty($colourPercentage))
    {
        $class0 = "green-arrow fa fa-arrow-up";
    }

    echo '<td class="">'.number_format($percentage).'%<i class="'.$class0.'"></i></td>';
?>
</tr>
<tr>
    <td><b><?php echo $year2?> Cml.</b></td>
    <?php
        $quantityyear2data = ltrim($quantityyear2data, '[');
        $quantityyear2data = rtrim($quantityyear2data, ']');
        $quantityyear2data = explode(',', $quantityyear2data);
        $runningTotal = 0;
        foreach ($quantityyear2data as $data)
        {
            $runningTotal += $data;
            echo '<td><b>'.number_format($runningTotal).'</b></td>';
        }
        echo '<td><b>'.number_format(array_sum($quantityyear2data)).'</b></td>';
    ?>
</tr>
<tr class="border-target">
    <td><div style="margin-top: -4px;">%</div></td>
<?php
    $runningTotalYear0 = 0;
    $runningTotalYear2 = 0;
    for ($i = 0; $i < 12; $i++)
    {
        $runningTotalYear0 += $quantityyear0data[$i];
        $runningTotalYear2 += $quantityyear2data[$i];
        
        $percentage = 0;
        if ($runningTotalYear2 != 0)
        {
            $percentage = ($runningTotalYear0 / $runningTotalYear2) * 100;
        }

        $colourPercentage = $percentage;
        (float)$percentage -= 100;

        if ($colourPercentage < (float)$G_kpithreshold1)
        {
            $class0 = "red-arrow fa fa-arrow-down";
        }

        if ($colourPercentage >= (float)$G_kpithreshold1 && $colourPercentage < (float)$G_kpithreshold2)
        {
            $class0 = "yellow-arrow fa fa-arrow-right";
        }

        if ($colourPercentage >= (float)$G_kpithreshold2)
        {
            $class0 = "green-arrow fa fa-arrow-up";
        }

        if (empty($colourPercentage))
        {
            $class0 = "green-arrow fa fa-arrow-up";
        }
?>
        <td><?php echo number_format($percentage); ?>%<i class="<?php echo $class0; ?>"></i></td>
<?php

    }

    if (isset($salesTargetForLastThreeYear[$year2]))
    {
        $totalTar2 = array_sum($salesTargetForLastThreeYear[$year2]);
        $totalmonthlysalespc2 = ($year2total / $totalTar2) * 100;
    }

    if ($totalmonthlysalespc2 < $G_kpithreshold2)
    {
        $classt2 = "bg-red";
    }

    if ($totalmonthlysalespc2 >= $G_kpithreshold2 AND $stotalmonthlysalespc2 < $G_kpithreshold2)
    {
        $classt2 = "bg-yellow";
    }

    if ($totalmonthlysalespc2 >= $G_kpithreshold2)
    {
        $classt2 = "bg-green";
    }

    if (empty($salesTargetForLastThreeYear[$year2]))
    {
        $classt2 = "bg-green";
    }

    $percentage = 0;
    if ($runningTotalYear2 != 0)
    {
        $percentage = ($runningTotalYear0 / $runningTotalYear2) * 100;
    }

    $colourPercentage = $percentage;
    (float)$percentage -= 100;

    if ($colourPercentage < (float)$G_kpithreshold1)
    {
        $class0 = "red-arrow fa fa-arrow-down";
    }

    if ($colourPercentage >= (float)$G_kpithreshold1 && $colourPercentage < (float)$G_kpithreshold2)
    {
        $class0 = "yellow-arrow fa fa-arrow-right";
    }

    if ($colourPercentage >= (float)$G_kpithreshold2)
    {
        $class0 = "green-arrow fa fa-arrow-up";
    }

    if (empty($colourPercentage))
    {
        $class0 = "green-arrow fa fa-arrow-up";
    }

    echo '<td class="">'.number_format($percentage).'%<i class="'.$class0.'"></i></td>';
?>
</tr>