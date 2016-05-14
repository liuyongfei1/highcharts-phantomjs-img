<?php
/**

生成json数据串
blog: phping.sinaapp.com
author: phping
*/ 
function generatecharts() { 
    $aperiod = 'month'; 
    $date = date("Y-m"); 
    $date = Yii::app()->request->getParam('date',date("Y-m"));
    // 拿到要查询的日期 
    if(date("m",strtotime($date)) == date("m",time())) $date = date("Y-m-d",strtotime("-1 day")); // 查询日期是本月时，查询前一天的数据 
    else $date = date("Y-m-t",strtotime($date)); // 获取上月最后一天的月数据

    $data_type = 3; // 月数据 $org_type = 0; // 整站

    // 获取数据，趋势图表展示用 
    $charts_data = Data::getCharts($date,$data_type,$org_type); // 根据自己的需求写逻辑拿到图表数据
    $series = $charts_data['yAxisChart1']; 
    $predictChart = $charts_data['predictChart1']; 
    $month = $charts_data['month']; 
    $xAxis = $charts_data['xAxis']; 
    $text = "$month"."签约目标:".number_format($predictChart);
    $result = ''; $start_fake_json = "{"; $result .= $start_fake_json;

    $end_fake_json = " credits: {enabled: false}, chart: { renderTo: 'chart3', zoomType: 'xy' }, legend: { verticalAlign: 'top', y: 10, borderWidth: 1 },

    title: { text: '' }, xAxis:{ categories: $xAxis, },

    plotOptions: { series: { dataLabels: { enabled: true }, } },

    yAxis: { min: 0, title: { text: '' }, gridLineColor:'#EEE', plotLines: [{ width: 1, color: '#aa4643', value: $predictChart, label: { text: '$text' }, zIndex: 1 }], }, series: $series }";

    $result .= $end_fake_json; 
    // 新建文件 
    $json_file_name = dirname(Yii::app()->BasePath).'/email_png'.'/201605'.'/json/'.date("Y-m-d")."_total_num.json"; 
    $fp = fopen($json_file_name, "w+"); 
    // 打开文件指针，创建文件 
    if(!is_writable($json_file_name)) { 
        die("文件:".$json_file_name."不可写，请检查!"); 
    } 
    fwrite($fp,$result); fclose($fp);
    return $json_file_name;
}

// 拿到 json数据串
// blog: phping.sinaapp.com
// author: phping
$infile = generatecharts();
//此处设置highcharts_convert.js文件的绝对路径
$highcharts_convert = "....../highcharts-convert.js" ;
$outfile = "....../img" ;  // 此处是生成的图片要存放的路径，可根据你的需要自行设置

//由于本项目我是使用的yii的console command来执行php脚本的.
// 执行命令
$command = "phantomjs $highcharts_convert -infile $infile -outfile $outfile -scale 2.5 -width 800 -constr Chart";

// 放在命令行执行
exec($command,$output,$return_var);

// 大家可以根据自己的情况来执行命令