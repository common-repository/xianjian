<html lang="zh">

<head>
    <meta charset="UTF-8">
    <title>先荐--报表中心</title>
    <?php include 'config_common.php'?>
    <?php include 'nav.php'?>
</head>

<body class="paradigm_body">
    <div id='paradigm_report_page' class='paradigm-app' v-cloak>
        <div class='_paradigm-content-plugIn'>
            <paradigm-nav @logo-click='logoClick'></paradigm-nav>
            <div class="_paradigm-breadcrumb-list">
                <ul>
                    <li class="_paradigm-breadcrumb-item" @click='logoClick'>
                        &lt;&lt;推荐栏列表
                </ul>
            </div>
            <div class='content-reports'>
                <div class="main-title">
                    <h1>我的报表</h1>
                    <el-button :disabled="!(reportList.length)" type="primary" @click="downLoad">
                        下载报表
                    </el-button>
                </div>
                <div class="filter-reports">
                    <div>
                        <!-- 选择报表 -->
                        <el-select v-model="sceneId" @change="getReport" placeholder="请选择">
                            <el-option v-for="item in sceneList" :key="item.value" :label="item.label" :value="item.value">
                            </el-option>
                        </el-select>
                    </div>
                    <el-date-picker :picker-options="pickerOptions" v-model="time" @change="getReport" :editable="false"
                        value-format="yyyy-MM-dd" type="daterange" range-separator="至" start-placeholder="开始日期"
                        end-placeholder="结束日期">
                    </el-date-picker>
                </div>
                <div class="report-table">
                    <el-table max-height="300" empty-text="暂无数据" :data="reportList" border style="width: 100%">
                        <el-table-column align="center" label="日期" min-width="150">
                            <template slot-scope="scope">
                                {{formartTime(scope.row.reportDate)}}
                            </template>
                        </el-table-column>
                        <el-table-column align="center" label="推荐栏展示PV" min-width="150">
                            <template slot-scope="scope">
                                {{scope.row.showPV}}
                            </template>
                        </el-table-column>
                        <el-table-column align="center" label="推荐栏点击PV" min-width="150">
                            <template slot-scope="scope">
                                {{scope.row.detailPageShowPV}}
                            </template>
                        </el-table-column>
                        <el-table-column align="center" label="推荐栏展示UV" min-width="160">
                            <template slot-scope="scope">
                                {{scope.row.showUV}}
                            </template>
                        </el-table-column>
                        <el-table-column align="center" label="推荐栏点击UV" min-width="160">
                            <template slot-scope="scope">
                                {{scope.row.detailPageShowUV}}
                            </template>
                        </el-table-column>
                        <el-table-column align="center" label="点击率" min-width="160">
                            <template slot-scope="scope">
                                {{getCtrPercent(scope.row.ctr)}}%
                            </template>
                        </el-table-column>

                    </el-table>
                </div>
                <div class="line-chart" :class="{'hide-chart': reportList.length === 0}">
                    <div id="line-box"></div>
                </div>
            </div>
            <?php include 'footer.php'?>
        </div>
    </div>

    </template>

    <script>
        // -2 代表所有场景
        function getDefaultScentList() {
            return [{
                value: -2,
                label: '所有场景'
            }]
        }
        /*
         * 有大量的页面都有获取日期的需求，这里抽取一个函数来方便获取日期
         * @params offset: number 相对于今天的日期偏移，比如1为明天，-1为昨天
         * @return Date 日期
         */
        function getDate(offset) {
            if (isNaN(offset)) offset = 0
            const date = new Date()
            date.setDate(date.getDate() + offset)
            return date
        }
        /**
         * 对日期进行格式化
         * @params date: Date
         * @return string 以yyyy-mm-dd格式标识的日期字符串
         */
        function formatDate(date) {
            let y = date.getFullYear()
            let m = String(date.getMonth() + 1).padStart(2, '0')
            let d = String(date.getDate()).padStart(2, '0')
            return `${y}-${m}-${d}`
        }
        /*
         * 直接获取格式化的日期
         * @params offset: number
         * @return string 以yyyy-mm-dd格式标识的日期字符串
         */
        function getFormatedDate(offset) {
            const date = getDate(offset)
            return formatDate(date)
        }
        const querySceneId = window._4paradigm_plug_util.getQueryVariable('sceneId')
        new Vue({
                el: '#paradigm_report_page',
                data() {
                    return {
                        sceneId: (querySceneId && Number(querySceneId)) || -2,
                        defaultTime: new Date(),
                        // 默认时间为一周前到一天前
                        time: [getFormatedDate(-7), getFormatedDate(-1)],
                        sceneList: getDefaultScentList(),
                        reportList: [],
                        pickerOptions: {
                            // 日期禁用状态
                            disabledDate(time, a) {
                                let now = new Date()
                                let y = now.getFullYear()
                                let m = String(now.getMonth() + 1).padStart(2, '0')
                                let date = String(now.getDate()).padStart(2, '0')
                                return time >= new Date(`${y}-${m}-${date} 00:00:00`)
                            }
                        },
                        titleMemoList: {
                            showPV: {
                                label: '推荐栏展示PV',
                                memo: '推荐结果接口请求次数，每次返回多个只计算一次'
                            },
                            detailPageShowPV: {
                                label: '推荐栏点击PV',
                                memo: '推荐内容中，用户点击总数'
                            },
                            showUV: {
                                label: '推荐栏展示UV',
                                memo: '有多少不同的用户查看推荐内容'
                            },
                            detailPageShowUV: {
                                label: '推荐栏点击UV',
                                memo: '有多少不同的用户点击推荐内容'
                            },
                            ctr: {
                                label: '推荐栏点击率',
                                memo: '推荐内容点击数与被展示数之比'
                            }
                        },

                        statusSteps: {
                            step1: 10, // 初始化状态
                            step2: 20, // 埋点配置
                            step2_1: 21, // API上报物料

                            step3: 30, // 内容 配置完成，sdk开始接入
                            step3_1: 31, // 渲染代码接入中或SDK接入中
                            step3_2: 32, // 埋点配置（SDK）
                            step3_3: 33, // 请求推荐结果
                            step3_4: 34, // 渲染代码完成或SDK接入完成

                            step4: 40, // 规则配置完成，待上线
                            step5: 50, // 已上线
                            step0: 0
                        },
                    };
                },
                mounted() {
                    this.init()
                },
                methods: {
                    logoClick() {
                        let phpUrl = window.location.origin + window.location.pathname;
                        window.location.href = phpUrl +
                            '?page=rec_xianjian_rec_options'
                    },
                    init() {
                        this.getParams()
                    },
                    getCtrPercent(point) {
                        var str = Number(point * 100).toFixed(3)
                        return str
                    },
                    formartTime(time) {
                        time = new Date(time)
                        let y = time.getFullYear()
                        let m = String(time.getMonth() + 1).padStart(2, '0')
                        let date = String(time.getDate()).padStart(2, '0')
                        return `${y}-${m}-${date}`
                    },
                    async getParams() {
                        let params = {}
                        let srcStr = ''
                        params = {
                            source: '7',
                            plugSiteId: document.getElementById('paradigm_sitId') && document.getElementById(
                                'paradigm_sitId').getAttribute(
                                'value') || ''
                        }
                        let [{
                            data: list
                        }, {
                            data: info
                        }] = await Promise.all([
                            window._4paradigm_plug_API.getSceneList(params),
                            window._4paradigm_plug_API.getUserInfo()
                        ])
                        this.sceneList = getDefaultScentList()
                        list.forEach(item => {
                            item.status > this.statusSteps.step3_4 &&
                                this.sceneList.push({
                                    value: item.id,
                                    label: item.name
                                })
                        })
                        this.sceneId = this.sceneId || this.sceneList[0].value
                        this.userInfo = info

                        // 每次获取参数完成后，获取报表的数据
                        this.getReport()
                    },
                    // 获取报表接口
                    async getReport() {
                        let params = {}
                        if (this.sceneId < 0) {
                            params['source'] = '7';
                            // 查看全部
                            params['sceneID'] = -2;
                        } else {
                            params['sceneID'] = this.sceneId
                        }
                        let {
                            data
                        } = await window._4paradigm_plug_API.getReport(
                            Object.assign(params, {
                                customID: this.userInfo.id,
                                begin: this.time[0],
                                end: this.time[1]
                            })
                        )
                        this.reportList = JSON.parse(data)

                        // if (!this.reportList.length) return
                        let xAxisData = this.reportList.map(item => item.reportDate)
                        xAxisData.reverse()
                        let legend = {
                            showPV: '推荐栏展示PV',
                            detailPageShowPV: '推荐栏点击PV',
                            showUV: '推荐栏展示UV',
                            detailPageShowUV: '推荐栏点击UV',
                            ctr: '推荐栏点击率'
                        }
                        let series = Object.keys(legend).map(item => {
                            return {
                                name: legend[item],
                                type: 'line',
                                stack: item,
                                yAxisIndex: item === 'ctr' ? 1 : 0,
                                data: this.reportList
                                    .map(p => {
                                        if (item === 'ctr') {
                                            return this.getCtrPercent(p[item])
                                        }
                                        return p[item]
                                    })
                                    .reverse(),
                                lineStyle: {
                                    type: item === 'ctr' ? 'dotted' : 'solid',
                                    width: 1.5
                                }
                            }
                        })
                        // 绘制折线图
                        this.initLine({
                            xAxisData,
                            series
                        })
                    },
                    // 下载表格
                    downLoad() {
                        let [customID, sceneID] = [this.userInfo.id, this.sceneId]
                        const [begin, end] = this.time
                        location.href = "https://nbrecsys.4paradigm.com" +
                            `/business/report/download?customID=${customID}&sceneID=${sceneID}&begin=${begin}&end=${end}`
                    },
                    // 绘制折线图
                    initLine({
                        xAxisData,
                        series
                    }) {
                        // 引入折线图
                        var line = echarts.init(document.getElementById('line-box'))
                        line.setOption({
                            color: ['#59c1c4', '#b34b43', '#eaa844', '#8230f4', '#5990dc'],
                            tooltip: {
                                trigger: 'axis'
                            },
                            legend: {
                                data: [
                                    '推荐栏展示PV',
                                    '推荐栏点击PV',
                                    '推荐栏展示UV',
                                    '推荐栏点击UV',
                                    '推荐栏点击率'
                                ],
                                orient: 'vertical',
                                top: '40px',
                                left: '24px',
                                itemGap: 20
                            },
                            grid: {
                                left: '18%',
                                right: '4%',
                                top: '50px',
                                containLabel: true,
                                borderColor: '#d5d9dd'
                            },
                            xAxis: {
                                axisLine: {
                                    lineStyle: {
                                        color: '#d5d9dd'
                                    }
                                },
                                splitLine: {
                                    show: true,
                                    lineStyle: {
                                        color: '#d5d9dd',
                                        type: 'dashed'
                                    }
                                },
                                type: 'category',
                                boundaryGap: true,
                                data: xAxisData
                            },
                            yAxis: [{
                                    type: 'value',
                                    axisLine: {
                                        lineStyle: {
                                            color: '#d5d9dd'
                                        }
                                    },
                                    splitLine: {
                                        show: true,
                                        lineStyle: {
                                            color: '#d5d9dd'
                                        }
                                    }
                                },
                                {
                                    name: '推荐栏点击率',
                                    // max: 100,
                                    type: 'value',
                                    formatter: '{value} %',
                                    axisLabel: {
                                        show: true,
                                        itemGap: 20,
                                        formatter: '{value} %'
                                    },
                                    axisLine: {
                                        lineStyle: {
                                            color: '#d5d9dd'
                                        }
                                    },
                                    splitLine: {
                                        show: true,
                                        lineStyle: {
                                            color: '#d5d9dd'
                                        }
                                    }
                                }
                            ],
                            series
                        })
                    }
                }
            },

        );
    </script>

</body>

</html>