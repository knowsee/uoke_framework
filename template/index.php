<? require_once getTemplate('header'); ?>
<!-- Preloader -->
    <div id="preloader">
        <div class="loader">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    
    <!-- home-page -->
    
    <div class="home-page">
        
        <!-- Introduction -->
        
        <div class="introduction">
            <!-- <div class="mask">
            </div> -->
            <div class="intro-content">
                <!-- <h1>HELLO<br>
                I'M <span>JOHN</span> DOE</h1> -->
                <h1>Private</h1> 
                <span>Actual</span><span class="number"></span>
                <p class="slogan-text text-capitalize">实资私募基金</p>

                <div class="social-media hidden-xs">
                    <a href="#" class="fa fa-weibo" data-toggle="tooltip" title="Facebook"></a>
                    <a href="#" class="fa fa-wechat" data-toggle="tooltip" title="Twitter"></a>
                    <a href="#" class="fa fa-qq" data-toggle="tooltip" title="Google+"></a>
                </div>
            </div>
            
            <!-- Social Media Icons [ END ] -->
        </div>
        
        <!-- Navigation Menu -->
        
        <div class="menu">
            <div class="profile-btn">
                <img alt="" src="<?=STATIC_URL?>img/about.jpg" style="width:100%; height:100%;">
                <div class="mask">
                </div>
                <div class="heading">
                    <i class="ion-ios-people-outline hidden-xs"></i>
                    <h2>我的基金</h2>
                </div>
            </div>
            
            <!-- Single Navigation Menu Button -->
            
            <div class="portfolio-btn">
                <img alt="" src="<?=STATIC_URL?>img/portfolio.jpg">
                <div class="mask">
                </div>
                <div class="heading">
                    <i class="ion-ios-briefcase-outline hidden-xs"></i>
                    <h2>基金报表</h2>
                </div>
            </div>
            
            <!-- Single Navigation Menu Button [ END ]  -->
            
            <div class="service-btn">
                <img alt="" src="<?=STATIC_URL?>img/service.jpg">
                <div class="mask">
                </div>
                <div class="heading">
                    <i class="ion-ios-lightbulb-outline hidden-xs"></i>
                    <h2>产品信息</h2>
                </div>
            </div>
            
            <!-- Single Navigation Menu Button [ END ]  -->
            
            <div class="contact-btn">
                <img alt="" src="<?=STATIC_URL?>img/contact.jpg">
                <div class="mask">
                </div>
                <div class="heading">
                    <i class="ion-ios-chatboxes-outline hidden-xs"></i>
                    <h2>联络我们</h2>
                </div>
            </div>
            
            <!-- Single Navigation Menu Button [ END ]  -->
            
        </div>
    </div>
    
    <!--
    4 ) Close Button
    -->
    
    <div class="close-btn"></div>
    
    <!--
    5 ) Profile Page
    -->
	<? require_once getTemplate('my'); ?>


    <? require_once getTemplate('status'); ?>
    <!--
    7 ) Service Page
    -->
    <? require_once getTemplate('cplan'); ?>
    <!--
    8 ) Contact Page
    -->
    <? require_once getTemplate('contact'); ?>
    <!--  
    9 ) Javascript
    - -->
    <script>
        var widthMax = ($(window).width()*($('.content-container').width()-12)/100);
        var mobile = true;
        if(widthMax > 400) {
            mobile = false;
        }
	$('#container, #containerMainList, #containerMain').css('width', widthMax>0 ? widthMax+'px' : ($(window).width()-40)+'px');
	
	$(function () {
    $('#container').highcharts({
        title: {
            text: '收益率曲线图',
            x: -20 //center
        },
        subtitle: {
            text: '与基金的收益率对比',
            x: -20
        },
        xAxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },
        yAxis: {
            title: {
                text: mobile == false ? '收益率%' : ''
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            crosshairs: true,
            shared: true
        },
        plotOptions: {
            spline: {
                marker: {
                    radius: 4,
                    lineColor: '#666666',
                    lineWidth: 1
                }
            }
        },
        series: [{
            name: '我的专属收益',
            data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
        }, {
            name: '基石基金收益',
            data: [-0.2, 0.8, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5]
        }]
    });
	$('#containerMainList').highcharts({
        title: {
            text: '收益表曲线图',
            x: -20 //center
        },
        xAxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },
        yAxis: {
            title: {
                text: mobile == false ? '收益率%' : ''
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            crosshairs: true,
            shared: true
        },
        plotOptions: {
            spline: {
                marker: {
                    radius: 4,
                    lineColor: '#666666',
                    lineWidth: 1
                }
            }
        },
        series: [{
            name: '基石基金收益',
            data: [-0.2, 0.8, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5]
        }]
    });
});
//基金报表
$(function () {
    $('#containerMain').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: '分布图'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
			headerFormat: '<span style="font-size: 14px">{point.key}</span><br/>',
			style: {
				fontSize: '14px'
			},
			valueSuffix: '%'
        },
        plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false,
						style: {
							fontSize: '14px'
						}
                    },
                    showInLegend: true
                }
            },
        series: [{
            name: "投资种类",
            colorByPoint: true,
            data: [{
                name: "股票投资",
                y: 60
            }, {
                name: "货币基金",
                y: 20,
                sliced: true,
                selected: true
            }, {
                name: "各类债券",
                y: 15
            }, {
                name: "期货投资",
                y: 5
            }]
        }]
    });
});
	</script>
	
	
<? require_once getTemplate('footer'); ?>