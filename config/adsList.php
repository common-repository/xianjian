<script type="application/javascript">
    // 广告状态列表
    Vue.component('ad-set', {
        name: 'AdsSet',
        props: {
            isAdsClosure: Boolean
        },
        data() {
            return {
                adsListData: []
            }
        },
        created() {
            this.getAdSceneList()
        },
        methods: {
            async getAdSceneList() {
                let {
                    data
                } = await window._4paradigm_plug_API.getAdSceneList()
                if (data) {
                    this.adsListData = data
                }
            },
            // 修改广告的状态
            async changeAdsState(item) {
                this.$confirm(`确认为场景【${item.name}】${item.needad === 1 ? '关闭' : '开启'}广告？`,
                        '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'warning'
                        }
                    )
                    .then(() => {
                        window._4paradigm_plug_API.sceneAdconfig({
                            sceneId: item.sceneId,
                            needAd: item.needad === 1 ? 0 : 1
                        }).then(({
                            code,
                            info
                        }) => {
                            if (code === 200) {
                                this.$message({
                                    type: 'success',
                                    message: '设置成功!',
                                    duration: 1500
                                })
                                // 刷新列表
                                this.getAdSceneList()
                            } else {
                                this.$message.error(info)
                            }
                        })
                    })
                    .catch(() => {})
            }
        },
        template: `  <div class="_paradigm-ads-list-scope">
                        <div v-for="(item,index) in adsListData" :key="index" class="_paradigm-plug-scence-list-item">
                        <div class="title-bar">
                            <span class="title">{{item.name}}</span>
                            <span class="ads-close-memo" v-if="isAdsClosure">流量异常，广告已被关闭</span>
                            <span class="ads-close-memo" v-else-if="!item.needad">您已经关闭了广告，这会影响您的收益，请及时打开哦</span>
                            <div class="operate-btn">
                            <el-button @click.stop="changeAdsState(item)" :disabled="isAdsClosure">{{item.needad===1 && !isAdsClosure?'关闭广告':'开启广告'}}</el-button>
                            </div>
                        </div>
                        <div class="_paradigm-online-item-scope">
                            <div class="report-item">
                            <div class="uv-icon report-icon">S</div>
                            <div class="report-div">
                                <div class="report-memo">
                                <span class="top-memo">昨日展现</span>
                                <span class="report-num">{{item.showNum}}</span>
                                </div>
                            </div>
                            </div>
                            <div class="report-item">
                            <div class="pv-icon report-icon">C</div>
                            <div class="report-div">
                                <div class="report-memo">
                                <span class="top-memo">昨日点击</span>
                                <span class="report-num">{{item.clickNum}}</span>
                                </div>
                            </div>
                            </div>
                            <div class="report-item">
                            <div class="ctr-icon report-icon">¥</div>
                            <div class="report-div">
                                <div class="report-memo">
                                <span class="top-memo">昨日收益</span>
                                <span class="report-num">{{Number(item.cost)/100}}元</span>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>`
    })
    // 收入明细
    Vue.component('ad-income-detail', {
        name: 'IncomeDetail',
        created() {
            this.getIncomDetailList()
        },
        data() {
            return {
                currentPage: 1,
                pageSize: 20,
                totalPages: 0,
                incomDetailList: [],
                sceneIncomeList: [],
                dialogTitle: '',
                showSceneList: false
            }
        },
        methods: {
            formartTime(time) {
                time = new Date(time)
                let y = time.getFullYear()
                let m = String(time.getMonth() + 1).padStart(2, '0')
                let date = String(time.getDate()).padStart(2, '0')
                return `${y}-${m}-${date}`
            },
            async showBySceneDialog(time) {
                this.dialogTitle = '【' + this.formartTime(time) + '】分场景查看'
                this.showSceneList = true
                let {
                    data
                } = await window._4paradigm_plug_API.getIncomDetailListByScene({
                    date: this.formartTime(time),
                    page: 1,
                    pageSize: 1000
                })
                if (data) {
                    this.sceneIncomeList = data.list
                }
            },
            async getIncomDetailList() {
                let {
                    data
                } = await window._4paradigm_plug_API.getIncomDetailList({
                    page: this.currentPage,
                    pageSize: this.pageSize
                })
                if (data) {
                    this.totalPages = data.allPage
                    this.incomDetailList = data.list
                }
            },
            // 当前页改变时的回调函数
            handleCurrentChange(curPage) {
                this.currentPage = curPage
                this.getIncomDetailList()
            },
            // 每页显示条数改变的回调
            handleSizeChange(pageSize) {
                this.currentPage = 1
                this.pageSize = pageSize
                this.getIncomDetailList()
            }
        },
        template: `<div class="_paradigm-income-detail-scope">
                    <el-table empty-text="暂无数据" :data="incomDetailList" border style="width: 100%">
                    <el-table-column align="center" label="按日期查看" min-width="150">
                        <template slot-scope="scope">
                        {{formartTime(scope.row.adDate)}}
                        </template>
                    </el-table-column>
                    <el-table-column align="center" label="分场景查看" min-width="150">
                        <template slot-scope="scope">
                        <el-button type='text' @click="showBySceneDialog(scope.row.adDate)">查看</el-button>
                        </template>
                    </el-table-column>
                    <el-table-column align="center" label="展现" min-width="150" prop='showNum' />
                    <el-table-column align="center" label="点击" min-width="160" prop='clickNum' />
                    <el-table-column align="center" label="收入（元）" min-width="160">
                        <template slot-scope="scope">
                        {{scope.row.cost/100}}
                        </template>
                    </el-table-column>
                    </el-table>
                    <!-- 表格分页 -->
                    <div class="page-btn" v-if="totalPages > 0">
                    <el-pagination @current-change="handleCurrentChange" :page-sizes="[20, 40, 60, 80,100]" @size-change="handleSizeChange" :current-page.sync="currentPage" layout="sizes,prev, pager, next, jumper" :page-count="totalPages" :page-size="pageSize">
                    </el-pagination>
                    </div>
                    <el-dialog :title="dialogTitle" :visible.sync="showSceneList">
                    <el-table max-height="300" empty-text="暂无数据" :data="sceneIncomeList" border style="width: 100%">
                        <el-table-column align="center" label="场景名称" min-width="130" prop='sceneName' />
                        <el-table-column align="center" label="展现" min-width="100" prop='showNum' />
                        <el-table-column align="center" label="点击" min-width="100" prop='clickNum' />
                        <el-table-column align="center" label="收入（元）" min-width="130" prop='cost'>
                        <template slot-scope="scope">
                            {{scope.row.cost/100}}
                        </template>
                        </el-table-column>
                    </el-table>
                    <div slot="footer" class="dialog-footer">
                        <el-button @click="showSceneList = false">取消</el-button>
                    </div>
                    </el-dialog>
                </div>`
    })
    // 月度收入
    Vue.component('ad-month-income', {
        name: 'MonthIncome',
        data() {
            return {
                currentPage: 1,
                pageSize: 20,
                totalPages: 0,
                monthIncomeList: []
            }
        },
        created() {
            this.getIncomMonthList()
        },
        methods: {
            async getIncomMonthList() {
                let {
                    data
                } = await window._4paradigm_plug_API.getIncomMonthList({
                    page: this.currentPage,
                    pageSize: this.pageSize
                })
                if (data) {
                    this.monthIncomeList = data.list
                    this.totalPages = data.allPage
                }
            },
            // 当前页改变时的回调函数
            handleCurrentChange(curPage) {
                this.currentPage = curPage
                this.getIncomMonthList()
            },
            // 每页显示条数改变的回调
            handleSizeChange(pageSize) {
                this.currentPage = 1
                this.pageSize = pageSize
                this.getIncomMonthList()
            }
        },
        template: `  <div class="_paradigm-month-income-scope">
                        <el-table max-height="300" empty-text="暂无数据" :data="monthIncomeList" border style="width: 100%">
                        <el-table-column align="center" label="按月查看" min-width="150" prop='adMonth' />
                        <el-table-column align="center" label="展现" min-width="150" prop='showNum' />
                        <el-table-column align="center" label="收入（元）" min-width="160">
                            <template slot-scope="scope">
                            {{scope.row.cost/100}}
                            </template>
                        </el-table-column>
                        </el-table>
                        <!-- 表格分页 -->
                        <div class="page-btn" v-if="totalPages > 0">
                        <el-pagination @current-change="handleCurrentChange" :page-sizes="[20, 40, 60, 80,100]" @size-change="handleSizeChange" :current-page.sync="currentPage" layout="sizes,prev, pager, next, jumper" :page-count="totalPages" :page-size="pageSize">
                        </el-pagination>
                        </div>
                    </div>`
    })
    // 付款记录
    Vue.component('ad-extract-record', {
        name: 'ExtractRecord',
        props: {
            isAdsClosure: Boolean
        },
        created() {
            this.getWithdrawsList()
        },
        data() {
            return {
                accountType: {
                    1: '支付宝',
                    2: '微信',
                    3: '银行卡'
                },
                extractRecordList: []
            }
        },
        methods: {
            formartTime(time) {
                time = new Date(time)
                let y = time.getFullYear()
                let m = String(time.getMonth() + 1).padStart(2, '0')
                return `${y}-${m}`
            },
            async getWithdrawsList() {
                let {
                    data
                } = await window._4paradigm_plug_API.withdrawsList()
                if (data) {
                    this.extractRecordList = data
                }
            }
        },
        template: `<div class="_paradigm-extract-record-scope">
                        <el-table empty-text="暂无数据" :data="extractRecordList" border style="width: 100%">
                        <el-table-column align="center" label="时间" min-width="150">
                            <template slot-scope="scope">
                            {{formartTime(scope.row.ctime)}}
                            </template>
                        </el-table-column>
                        <el-table-column align="center" label="财务对象" min-width="150">
                            <template slot-scope="scope">
                            {{accountType[scope.row.type||1]}}
                            </template>
                        </el-table-column>
                        <el-table-column align="center" label="提现金额（元）" min-width="160">
                            <template slot-scope="scope">
                            {{scope.row.amount/100}}
                            </template>
                        </el-table-column>
                        <el-table-column align="center" label="代扣税（元）" min-width="160" prop='tax'>
                            <template slot-scope="scope">
                            {{scope.row.tax/100}}
                            </template>
                        </el-table-column>
                        <el-table-column align="center" label="实付款（元）" min-width="160">
                            <template slot-scope="scope">
                            {{(scope.row.amount-scope.row.tax)/100}}
                            </template>
                        </el-table-column>
                        <el-table-column align="center" label="状态" min-width="160">
                            <template slot-scope="scope">
                            <span :class="scope.row.status===10?'_paradigm-extract-record-status success':(isAdsClosure?'_paradigm-extract-record-status danger':'')">{{scope.row.status===10?'已付款':(isAdsClosure?'已封禁':'付款中')}}</span>
                            </template>
                        </el-table-column>
                        </el-table>
                    </div>`
    })
    // 财务信息
    Vue.component('ad-account-set', {
        name: 'AccountSet',
        props: {
            isAdsClosure: Boolean,
            adAccountData: Object
        },
        created() {
            if (
                this.adAccountData &&
                this.adAccountData.alipayAccount &&
                this.adAccountData.alipayRealName
            ) {
                this.accountData.id = this.adAccountData.alipayAccount
                this.accountData.name = this.adAccountData.alipayRealName
                this.submitFinish = true
            }
        },
        data() {
            return {
                accountData: {
                    id: '',
                    name: ''
                },
                submitFinish: false
            }
        },
        methods: {
            // 提交财务信息
            submitAccountData() {
                this.$confirm(
                        `确认提交财务信息?【支付宝账号：${
                            this.accountData.id
                            }】，【收款人姓名：${
                            this.accountData.name
                            }】，确认提交后不允许修改，请谨慎操作。`,
                        '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'warning'
                        }
                    )
                    .then(() => {
                        window._4paradigm_plug_API.saveAlipayAccount({
                            alipayAccount: this.accountData.id,
                            alipayRealName: this.accountData.name
                        }).then(({
                            code,
                            info
                        }) => {
                            if (code === 200) {
                                this.$message({
                                    type: 'success',
                                    message: '提交财务信息成功!',
                                    duration: 1500
                                })
                                this.submitFinish = true
                                this.$emit('refresh')
                            } else {
                                this.$message.error(info)
                            }
                        })
                    })
                    .catch(() => {})
            }
        },
        template: ` <div class="_paradigm-account-set-scope">
                        <div class="_paradigm-account-set-memo" v-if="!isAdsClosure">
                        <p>尊敬的先荐用户您好：</p>
                        <p>如果账户余额不满100元则不可提现。</p>
                        <p>支付宝信息确认提交后不允许修改，请谨慎填写。</p>
                        <p>收款人姓名应与支付宝实名信息一致，否则不会打款。</p>
                        <p>每月1-5日为提现日，5-10日开始陆续打款，请已申请提现的站长耐心等待。</p>
                        </div>
                        <div class="_paradigm-account-set-item">
                        <label>收款支付宝：</label>
                        <el-input placeholder="请输入收款支付宝账号" v-model="accountData.id" :disabled="submitFinish"></el-input>
                        </div>
                        <div class="_paradigm-account-set-item">
                        <label>收款人姓名：</label>
                        <el-input placeholder="请输入收款人姓名" v-model="accountData.name" :disabled="submitFinish"></el-input>
                        </div>
                        <el-button type='primary' @click="submitAccountData()" class="_paradigm-account-set-btn" v-if="!submitFinish" :disabled="!accountData.id|| !accountData.name">提交</el-button>
                    </div>`
    })
    // 广告收益的总页面
    Vue.component('ad-list-page', {
        name: 'AdsList',
        created() {
            this.getAdAccount()
            this.getNeedAdFlg()
        },
        data() {
            return {
                // 账号被封禁
                isAdsClosure: false,
                isInitFlg:true,
                adsTabList: [{
                        index: 'ads_set',
                        name: '广告状态'
                    },
                    {
                        index: 'income_detail',
                        name: '收入明细'
                    },
                    {
                        index: 'month_income',
                        name: '月度收入'
                    },
                    {
                        index: 'extract_record',
                        name: '付款记录'
                    },
                    {
                        index: 'account_set',
                        name: '财务信息'
                    }
                ],
                adAccountData: {
                    lastMonthAmount: '',
                    lastDayAmount: '',
                    currentMonthAmount: '',
                    balance: '',
                    alipayAccount: '',
                    alipayRealName: ''
                },
                adsTabIndex: 'ads_set'
            }
        },
        methods: {
            switchTabList(index) {
                this.adsTabIndex = index
            },
            // 获取是否需要广告
            async getNeedAdFlg() {
                let {
                    data
                } = await window._4paradigm_plug_API.getUserInfo()
                if (data) {
                    this.isInitFlg = false
                    this.isAdsClosure = !data.needAd || data.needAd !== 1
                }
            },
            // 提现
            async applyToCashWithdrawal() {
                // 未提交账号信息
                if (
                    !this.adAccountData ||
                    !this.adAccountData.alipayAccount ||
                    !this.adAccountData.alipayRealName
                ) {
                    this.$alert(
                        '您尚未提交财务信息，若要提现，请先提交财务信息！',
                        '提示', {
                            confirmButtonText: '确定',
                            callback: action => {
                                this.adsTabIndex = 'account_set'
                            }
                        }
                    )
                    return
                }
                let {
                    code,
                    info
                } = await window._4paradigm_plug_API.withdrawsApply()
                if (code === 200) {
                    this.$message.success('提现申请成功，本月5-10日为打款日，请耐心等待')
                } else if (code === 3104) {
                    this.$message.error('您的余额不足100元，暂不支持提现操作！')
                } else {
                    this.$message.error(info)
                }
            },
            // 获取商家账号信息
            async getAdAccount() {
                let {
                    data
                } = await window._4paradigm_plug_API.getAdAccount()
                if (data) {
                    this.adAccountData = data
                }
            }
        },
        template: `   <div id="paradigm_scene_list" v-if='!isInitFlg'>
                        <div class="_paradigm-ads-tab">
                        <div v-for="item in adsTabList" :key="item.index" :class="item.index===adsTabIndex?'_paradigm-ads-tab-item active':'_paradigm-ads-tab-item'" @click="switchTabList(item.index)">
                            {{item.name}}
                        </div>
                        </div>
                        <div class="_paradigm-ads-nav-data">
                        <div class="_paradigm-ads-nav-data-item">上月收入：{{adAccountData.lastMonthAmount/100}}元</div>
                        <div class="_paradigm-ads-nav-data-item">昨日收入：{{adAccountData.lastDayAmount/100}}元</div>
                        <div class="_paradigm-ads-nav-data-item">当月收入：{{adAccountData.currentMonthAmount/100}}元</div>
                        <div class="_paradigm-ads-nav-data-item">账户余额：{{adAccountData.balance/100}}元</div>
                        <el-button :disabled="isAdsClosure" @click="applyToCashWithdrawal" class="_paradigm-ads-nav-data-btn" type='primary'>提现</el-button>
                        </div>
                        <template v-if="isAdsClosure">
                        <span class="_paradigm-ads-isAdsClosure">尊敬的站长您好：由于您的流量异常，暂时对您网站的广告进行了封禁，先荐内容推荐可以正常使用，如果您有什么异议，请联系客服QQ:937771886</span>
                        </template>
                        <template v-else-if="adsTabIndex!=='account_set'">
                        <p class="_paradigm-ads-alert first">不足100元不能提现，第一次提现请先填写财务信息。</p>
                        <p class="_paradigm-ads-alert">每月1-5日为提现日，5-10日为打款日，为了不影响各位站长收钱，请每月1-5日积极申请提现。</p>
                        <p class="_paradigm-ads-alert">账户余额仅为截止上月月底未提现的金额，不累计本月收入，如果提现之后，本月账户余额为0，请不要大惊小怪哦。</p>
                        </template>
                        <ad-set :isAdsClosure='isAdsClosure' v-if="adsTabIndex==='ads_set'" />
                        <ad-income-detail v-if="adsTabIndex==='income_detail'" />
                        <ad-month-income v-if="adsTabIndex==='month_income'" />
                        <ad-extract-record :isAdsClosure='isAdsClosure' v-if="adsTabIndex==='extract_record'" />
                        <ad-account-set :isAdsClosure='isAdsClosure' :adAccountData='adAccountData' @refresh='getAdAccount' v-if="adsTabIndex==='account_set'" />
                    </div>`
    })
</script>