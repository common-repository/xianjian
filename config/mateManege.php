<html lang="zh">

<head>
    <meta charset="UTF-8">
    <title>先荐--内容管理</title>
    <?php include 'config_common.php'?>
    <?php include 'nav.php'?>
</head>

<body class="paradigm_body">
    <div id='paradigm_mate_page' class='paradigm-app' v-cloak>
        <div class='_paradigm-content-plugIn materials-detail-scope' @click="hideAllCopy()" ref='materialsDetailContent'
            id='materials-detail-scope'>
            <paradigm-nav @logo-click='logoClick'></paradigm-nav>
            <div class="_paradigm-breadcrumb-list">
                <ul>
                    <li class="_paradigm-breadcrumb-item" @click='logoClick'>
                        &lt;&lt;推荐栏列表
                </ul>
            </div>
            <div class='content-mates'>
                <div class="main-title">
                    <h1>内容管理</h1>
                    <el-dropdown @command="handleViewReportCommand">
                        <span class="el-dropdown-link">
                            更新报表
                            <i class="el-icon-arrow-down el-icon--right"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item command="viewLogs">更新日志</el-dropdown-item>
                            <el-dropdown-item command="viewOperationDataList">查看操作数据</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                    <el-button class="addRule" @click="addFilterRule" v-if="!filterRule.isShow">
                        <i class="icon-add"></i>添加过滤规则
                    </el-button>
                </div>

                <!-- 过滤规则设置 start -->
                <div class="mate-filter-rule-scope" v-show="filterRule.isShow">
                    <template v-if="!initFlg">
                        <!-- 修改过滤规则 -->
                        <el-card class="mate-filter-rule-edit-scope" v-if="filterRule.isEdit">
                            <div slot="header">
                                <span>{{filterRule.title}}</span>
                                <div class="operation-rule-ope">
                                    <el-button @click="checkFromExit">从已有规则中选择</el-button>
                                </div>
                            </div>
                            <div class="rule-item">
                                <span class="rule-show-item-label">内容类别</span>
                                <div class="rule-show-item-info">
                                    <rule-tag-bar :tag-list='filterRule.categoryV1s' @edit-tag='selectCategory'
                                        no-data-alert='未选中任何类别' type='edit' />
                                </div>
                            </div>
                            <div class="rule-item">
                                <span class="rule-show-item-label">内容时效</span>
                                <div class="rule-show-item-info">
                                    <el-radio-group v-model="filterRule.mateTime" class="rule-radio-group">
                                        <el-radio :key=0 :label=-1>全部推荐</el-radio>
                                        <el-radio :key=1 :label=1>只推荐
                                            <el-input-number v-model="filterRule.timeValue" class="rule-input-num" :min='1'></el-input-number>
                                            天内内容
                                        </el-radio>
                                    </el-radio-group>
                                </div>
                            </div>
                            <div class="rule-item">
                                <span class="rule-show-item-label">内容类型</span>
                                <div class="rule-show-item-info">
                                    <el-radio-group v-model="filterRule.coverUrlCnt" class="rule-radio-group">
                                        <el-radio :key=0 :label=-1>全部内容</el-radio>
                                        <el-radio :key=1 :label=1>仅推荐有图内容</el-radio>
                                    </el-radio-group>
                                </div>
                            </div>
                            <div class="rule-item">
                                <div class="rule-opr-bar">
                                    <el-button type="primary" @click="confirmEditRule" :disabled="filterRule.itemNumber<limitNum">保存</el-button>
                                    <el-button @click="hideEditRule">取消</el-button>
                                    <span class="fit-count">符合规则内容数：{{filterRule.itemNumber}}条</span>
                                    <span class="limit-count-alert" v-if="filterRule.itemNumber<limitNum">
                                        <i class="el-icon-warning"></i>符合内容过少，请调整规则</span>
                                </div>
                            </div>
                            <rule-tag-select ref="RuleTagSelect" @confirm-edit-tag='tagSelectConfirm'></rule-tag-select>
                            <rule-exist-select ref="RuleExistSelect" @confirm-select-rule='checkExist'></rule-exist-select>
                        </el-card>
                        <!-- 展示过滤规则 -->
                        <div class="mate-filter-rule-info-scope" v-if="!filterRule.isEdit">
                            <div class="mate-filter-rule-info-item">
                                <label>当前规则</label>
                                <span>{{filterRule.title}}</span>
                            </div>
                            <div class="mate-filter-rule-info-item">
                                <label>符合规则内容数</label>
                                <span>{{filterRule.itemNumber}}条</span>
                            </div>
                            <div class="mate-filter-rule-info-item">
                                <label>内容类别</label>
                                <span class="mate-type-info">
                                    <rule-tag-bar :tag-list='filterRule.categoryV1s' no-data-alert='未选中任何类别' type='view' />
                                </span>
                            </div>
                            <div class="mate-filter-rule-info-item">
                                <label>内容时效</label>
                                <span>{{filterRule.mateTime===-1?'全部推荐':'推荐'+filterRule.timeValue+'天内内容'}}</span>
                            </div>
                            <div class="mate-filter-rule-info-item">
                                <label>内容类型</label>
                                <span>{{filterRule.coverUrlCnt===-1?'全部内容':'仅推荐有图内容'}}</span>
                            </div>
                            <div class="mate-filter-rule-info-opr">
                                <button @click="editRule">
                                    <i class="el-icon-edit-outline"></i>编辑规则</button>
                                <span class="btn-divider">|</span>
                                <button @click="deleteRule">
                                    <i class="el-icon-delete"></i>删除规则</button>
                            </div>
                        </div>
                    </template>
                    <div class="filter-divider-bottom"></div>
                </div>
                <!-- 过滤规则设置 end -->
                <div class="search-itemid">
                    <!-- 搜索栏 -->
                    <mate-search :config='searchConfig' @search='searchList'></mate-search>
                    <!-- 操作栏 -->
                    <!-- <div class="search-opr-bar">
                        <span class="select-ctn-label" :class="selectItemCount>0?'active':''">已选中{{selectItemCount}}项内容</span>
                        <el-button :disabled="selectItemCount===0" @click="batchWeightSet">
                            <i class="mate-icon mate-icon-btn-weight"></i>权重分</el-button>
                        <el-button :disabled="selectItemCount===0" @click="batchPushSet">
                            <i class="mate-icon mate-icon-btn-push"></i>必推时效</el-button>
                        <el-button :disabled="selectItemCount===0" @click="batchTopSet">
                            <i class="mate-icon mate-icon-btn-top"></i>置顶</el-button>
                    </div> -->
                </div>

                <div class="el-icon-d-arrow-left table-scroll-back" @click="tableScrollLeft" v-if="materialsList.length && showScrollBtn"
                    :style='goBackStyle'></div>
                <el-table v-if="materialsList.length" empty-text="没有数据" :data="materialsList" border @sort-change='sortChange'
                    width="100%" :max-height="tableMaxHeight" ref="mateTable">
                    <!-- <el-table-column type="selection" width="36" align="center">
                    </el-table-column> -->
                    <el-table-column :resizable="false" fixed label="内容ID/名称" width="300">
                        <template slot-scope="scope">
                            <div>
                                <div class="text-grey">{{scope.row.itemId}}</div>
                                <span class="cell-text" :title="scope.row.title">
                                    <mate-search-mark-cell :label='scope.row.title' :key-word='searchConfig.searchContent' />
                                </span>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column :resizable="false" fixed label="发布时间" :sortable='isEsSearch?true:"custom"' prop="publishTime"
                        width="110">
                        <template slot-scope="scope">
                            {{formatTime(scope.row.publishTime)}}
                        </template>
                    </el-table-column>
                    <el-table-column :resizable="false" label="封禁" align="center" width="86">
                        <template slot-scope="scope">
                            <div>
                                <el-switch @change='editBan(scope.row.isRecommend,scope.row.itemId)' v-model="scope.row.isRecommend"
                                    :active-value='0' :inactive-value='1'></el-switch>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column :resizable="false" label="权重分" align="center" width="74">
                        <template slot-scope="scope">
                            <div class="enable-edit" @click="showWeight(scope.row)">
                                {{scope.row.sceneItemRuleMap &&
                                scope.row.sceneItemRuleMap[mateRuleEnum.WEIGHT]?getRuleValue(scope.row,mateRuleEnum.WEIGHT)
                                : '未设置'}}
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column :resizable="false" label="必推时效" width="200">
                        <template slot-scope="scope">
                            <div class="enable-edit" @click="showPush(scope.row)">
                                <i class="mate-icon mate-icon-push" :class="getRuleValue(scope.row,mateRuleEnum.PUSH) === 1 ?'active':''"></i>
                                {{getRuleValue(scope.row,mateRuleEnum.PUSH) === 1 ?
                                formatTime(scope.row.sceneItemRuleMap[mateRuleEnum.PUSH].beginTime * 1000) + '—' +
                                formatTime(scope.row.sceneItemRuleMap[mateRuleEnum.PUSH].endTime * 1000) : '非必推'}}
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column :resizable="false" label="置顶" width="200">
                        <template slot-scope="scope">
                            <div class="enable-edit" @click="showMaterialTop(scope.row)">
                                <i class="mate-icon mate-icon-top" :class="getRuleValue(scope.row,mateRuleEnum.TOP) === 1 ?'active':''"></i>
                                {{getRuleValue(scope.row,mateRuleEnum.TOP) === 1 ? getTopTimeShow(scope.row) : '未置顶'}}
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column :resizable="false" label='展示量' width="120" align="center" prop="item4pdShowCnt">
                        <template slot-scope="scope">
                            <span class="cell-text" :title="scope.row['item4pdShowCnt']">{{scope.row['item4pdShowCnt']}}</span>
                        </template>
                    </el-table-column>
                    <el-table-column :resizable="false" label='点击量' width="120" align="center" prop="item4pdClickCnt">
                        <template slot-scope="scope">
                            <span class="cell-text" :title="scope.row['item4pdClickCnt']">{{scope.row['item4pdClickCnt']}}</span>
                        </template>
                    </el-table-column>
                    <el-table-column :resizable="false" label='点击率' width="120" align="center">
                        <template slot-scope="scope">
                            <span class="cell-text" :title="getColValue(scope.row)">{{getColValue(scope.row)}}</span>
                        </template>
                    </el-table-column>
                    <el-table-column :resizable="false" label="内容分类 ID" width="180" align="left">
                        <template slot-scope="scope">
                            <span class="cell-text" :title="scope.row.categoryId">
                                <mate-search-mark-cell :label='scope.row.categoryId' :key-word='searchConfig.searchContent' />
                            </span>
                        </template>
                    </el-table-column>
                    <el-table-column :resizable="false" label="内容" width="300">
                        <template slot-scope="scope">
                            <span class="cell-text" :title="scope.row.content">
                                <mate-search-mark-cell :label='scope.row.content' :key-word='searchConfig.searchContent' />
                            </span>
                        </template>
                    </el-table-column>
                    <el-table-column :resizable="false" label="发布者 ID" width="180" align="left">
                        <template slot-scope="scope">
                            <span class="cell-text" :title="scope.row.publisherId">
                                <mate-search-mark-cell :label='scope.row.publisherId' :key-word='searchConfig.searchContent' />
                            </span>
                        </template>
                    </el-table-column>
                    <template v-for="(col, index) in columns">
                        <el-table-column :resizable="false" :key="'col-'+index" :label="theadTitle[col] || col" :width="columnWidth[col]||300"
                            :align="getAlignByCol(col)">
                            <template slot-scope=" scope ">
                                <div @click.stop='showInput(scope.row,col)'>
                                    <el-input disabled v-model="scope.row[col] " v-if="col==='url' && scope.row.copyFlg "></el-input>
                                    <span class="cell-text" :title="scope.row[col]" v-else>{{scope.row[col]}}</span>
                                </div>
                            </template>
                        </el-table-column>
                    </template>
                </el-table>
                <el-table v-if="materialsList.length === 0" empty-text="暂无数据" border width="100%" class="noDataTable">
                </el-table>
                <!-- 表格分页 -->
                <div class="page-btn" v-if="totalPages > 0">
                    <el-pagination @current-change="handleCurrentChange" :page-sizes="[20, 40, 60, 80,100]"
                        @size-change="handleSizeChange" :current-page.sync="currentPage" layout="sizes,prev, pager, next, jumper"
                        :page-count="totalPages" :page-size="pageSize">
                    </el-pagination>
                </div>
                <!-- 更新日志弹框 -->
                <update-logs ref="UpdateLogs"></update-logs>
                <operation-data-list ref="OperationDataList"></operation-data-list>
                <!-- 修改权重弹窗 -->
                <mate-weight-set :api-params='apiParams' @refresh-list='refreshList' ref="MateWeightSet"></mate-weight-set>
                <!-- 修改必推弹窗 -->
                <mate-recom-set :api-params='apiParams' @refresh-list='refreshList' ref="MateRecomSet"></mate-recom-set>
                <!-- 置顶弹窗 -->
                <mate-top-set :api-params='apiParams' @refresh-list='refreshList' ref="MateTopSet"></mate-top-set>
                <!-- 编辑内容过多提醒 -->
                <mate-opr-ctn-alert ref="MateOprCtnAlert"></mate-opr-ctn-alert>
            </div>
            <?php include 'footer.php'?>
        </div>
    </div>
    <script>
        let querySceneId = window._4paradigm_plug_util.getQueryVariable('sceneId')
        querySceneId = (querySceneId && Number(querySceneId)) || 0
        let queryItemSetId = window._4paradigm_plug_util.getQueryVariable('itemSetId')
        queryItemSetId = (queryItemSetId && Number(queryItemSetId)) || 0
        /*
         * 内容库规则操作
         */
        const mateRuleEnum = {
            TOP: 1, // 置顶
            PUSH: 2, // 必推
            WEIGHT: 3 // 权重
        }
        Vue.component('mate-search-mark-cell', {
            props: {
                label: '',
                keyWord: ''
            },
            methods: {
                getShowHtml() {
                    const reg = new RegExp('(' + this.keyWord + ')', 'g')
                    return this.label.replace(reg, "<font color='#f85669'>$1</font>")
                }
            },
            template: ` <span v-html="getShowHtml()" :class="label==='【空】'?'mate-search-mark-cell is-null':''"></span>`
        })
        // 定义一个名为 rule-tag-bar 的新组件
        Vue.component('rule-tag-bar', {
            data() {
                return {
                    tagConfig: {
                        isShowMore: false,
                        isShowAll: false
                    },
                    showTagList: []
                }
            },
            mounted() {
                this.clampList()
                if (this.type === 'edit') {
                    window._4paradigm_plug_util.bindResizeWindow(this.clampList)
                }
            },
            beforeDestroy() {
                window._4paradigm_plug_util.unbindResizeWindow()
            },
            props: {
                tagList: {},
                noDataAlert: {},
                // 显示类型，分为edit(编辑,用于编辑画面)/view(查看，用于查看画面)/list(列表，用于选择列表)
                type: {}
            },
            methods: {
                editTag() {
                    this.$emit('edit-tag')
                },
                clampList(maxHeight) {
                    this.showTagList = [...this.tagList]
                    const typeHeight = {
                        edit: 80,
                        view: 12,
                        list: 46
                    }
                    if (!maxHeight) {
                        maxHeight = typeHeight[this.type]
                    }
                    setTimeout(() => {
                        window._4paradigm_plug_util.tclamp(
                            this.tagConfig,
                            this.$refs.ruleTagBar,
                            this.showTagList,
                            maxHeight
                        )
                    }, 200)
                },
                showAllTag() {
                    this.tagConfig.isShowAll = !this.tagConfig.isShowAll
                    if (this.tagConfig.isShowAll) {
                        this.clampList(8000)
                    } else {
                        this.clampList(46)
                    }
                }
            },
            watch: {
                tagList: {
                    handler(curVal) {
                        this.clampList()
                    },
                    deep: true
                }
            },
            components: {},
            template: `<div class="rule-tag-bar" :class="type" ref="ruleTagBar">
                    <div class="tag-edit" @click='editTag()' v-if="type ==='edit' ">编辑</div>
                    <div class="tag-more" v-if="tagConfig.isShowMore && type ==='view'">...</div>
                    <template v-if="type ==='list'">
                        <div class="tag-more" v-if="tagConfig.isShowMore" @click='showAllTag()'>
                            <el-button>
                                <i class="el-icon-more"></i>
                            </el-button>
                        </div>
                        <div class="tag-more" v-if='tagConfig.isShowAll' @click='showAllTag()'>
                            <el-button>
                                <i class="el-icon-arrow-up"></i>
                            </el-button>
                        </div>
                    </template>
                    <div v-for="(item,index) in showTagList" :key="index" :class="item===''?'rule-tag is-null':'rule-tag'">
                        {{item?item:'【空】'}}
                    </div>
                    <span v-if="showTagList.length==0" class="noDataAlert">{{noDataAlert}}</span>
                </div>`
        })
        Vue.component('rule-tag-select', {
            data() {
                return {
                    options: [],
                    showDialog: false,
                    searchData: '',
                    checkAll: false
                }
            },
            computed: {
                showOptions() {
                    return this.searchData.trim() ?
                        this.options.filter(item => {
                            return item.name.indexOf(this.searchData.trim()) > -1
                        }) :
                        this.options
                }
            },
            methods: {
                show(options) {
                    this.options = options
                    this.searchData = ''
                    this.showDialog = true
                    this.computeCheckAll()
                },
                hide() {
                    this.showDialog = false
                },
                confirm() {
                    this.hide()
                    this.$emit('confirm-edit-tag', this.options)
                },
                chooseAll() {
                    this.showOptions.forEach(element => {
                        element.checked = this.checkAll
                    })
                },
                deleteCheck(item) {
                    item.checked = false
                    this.computeCheckAll()
                },
                computeCheckAll() {
                    let allSelect = true
                    this.showOptions.forEach(element => {
                        if (!element.checked) {
                            allSelect = false
                        }
                    })
                    this.checkAll = allSelect
                }
            },
            watch: {
                searchData(val) {
                    this.computeCheckAll()
                }
            },
            template: `<el-dialog :visible.sync="showDialog" custom-class="rule-tag-select">
                    <div class="tag-select-title" slot="title">
                        <span class="title-span">内容类别</span>
                        <span class="memo-span">（获取完整内容类别可能需要时间，请耐心等待）</span>
                    </div>
                    <div>
                        <div class="search-bar">
                            <el-input placeholder="输入关键词搜索类别" prefix-icon="el-icon-search" v-model="searchData"></el-input>
                        </div>
                        <div class="tag-select-content">
                            <div class="show-column">
                                <div class="column-title">
                                    <el-checkbox v-model="checkAll" @change="chooseAll" v-if="showOptions.length!==0">
                                    </el-checkbox>
                                    <span class="checkbox-column-title">{{searchData.trim()?'搜索结果':'全部类别'}}</span>
                                </div>
                                <div class="column-content">
                                    <div v-for="(item,index) in showOptions" :key="index" class="select-item">
                                        <el-checkbox v-model="item.checked" @change='computeCheckAll'>
                                            <mate-search-mark-cell :label='item.name?item.name:"【空】" ' :key-word='searchData' />
                                        </el-checkbox>
                                    </div>
                                    <span v-if="showOptions.length===0 && searchData" class="no-fit-dtata">无匹配结果</span>
                                </div>
                            </div>
                            <div class="show-column">
                                <div class="column-title">
                                    已选类别
                                </div>
                                <div class="column-content">
                                    <div v-for="(item,index) in options" :key="index" :class="item.name===''?'select-item is-null':'select-item'" v-if="item.checked">
                                        <el-button type="text" @click="deleteCheck(item)">
                                            <i class="el-icon-close" />
                                        </el-button>{{item.name?item.name:"【空】"}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div slot="footer" class="dialog-footer">
                        <el-button @click="hide">取消</el-button>
                        <el-button type="primary" @click="confirm">确定</el-button>
                    </div>
                </el-dialog>`
        })

        Vue.component('rule-exist-select', {
            data() {
                return {
                    limitNum: 100,
                    showDialog: false,
                    sceneId: '',
                    ruleList: []
                }
            },
            computed: {},
            methods: {
                formatTime(ctime) {
                    let time = new Date(ctime || 0)
                    let y = time.getFullYear()
                    let m = String(time.getMonth() + 1).padStart(2, '0')
                    let date = String(time.getDate()).padStart(2, '0')
                    return `${y}-${m}-${date}`
                },
                show(sceneId) {
                    this.ruleList = []
                    this.sceneId = sceneId
                    this.showDialog = true
                    this.getAllFilterRule()
                },
                hide() {
                    this.showDialog = false
                },
                selectRule(rule) {
                    this.hide()
                    this.$emit('confirm-select-rule', rule)
                },
                async getAllFilterRule() {
                    let {
                        data
                    } = await window._4paradigm_plug_API.getAllFilterRule({
                        sceneId: this.sceneId
                    })
                    if (data) {
                        this.ruleList = data
                        this.ruleList.forEach(item => {
                            if (!item.categorys) {
                                item.categorys = []
                            }
                        })
                    } else {
                        this.ruleList = []
                    }
                }
            },
            template: ` <el-dialog :visible.sync="showDialog" custom-class="rule-exist-select" title='从已有规则中选择'>
                    <div>
                        <el-table empty-text="暂无数据" :data="ruleList" width="100%" max-height="470" ref="mateTable">
                            <el-table-column :resizable="false" label="规则名称" prop="sceneName" width="140" />
                            <el-table-column :resizable="false" label="最后修改日期" width="120">
                                <template slot-scope="scope">
                                    {{formatTime(scope.row.selectionRule.lastChangeTime)}}
                                </template>
                            </el-table-column>
                            <el-table-column :resizable="false" label="符合规则内容数" width="172">
                                <template slot-scope="scope">
                                    <span>{{scope.row.selectionRule.itemNumber}}条</span>
                                    <span class="limit-num" v-if="scope.row.selectionRule.itemNumber<limitNum">
                                        <i class="el-icon-warning"></i>内容过少，请调整</span>
                                </template>
                            </el-table-column>
                            <el-table-column :resizable="false" label="内容类别" width="200">
                                <template slot-scope="scope">
                                    <div class="category-column">
                                        <rule-tag-bar :tag-list='scope.row.categorys' no-data-alert='未选中任何类别' type='list' />
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column :resizable="false" label="内容时效" width="100">
                                <template slot-scope="scope">
                                    {{scope.row.selectionRule.publishTime===-1?'全部推荐':+scope.row.selectionRule.publishTime+'天内内容'}}
                                </template>
                            </el-table-column>
                            <el-table-column :resizable="false" label="内容类型" width="100">
                                <template slot-scope="scope">
                                    {{scope.row.selectionRule.coverUrlCnt===-1?'全部内容':'仅推荐有图内容'}}
                                </template>
                            </el-table-column>
                            <el-table-column :resizable="false" label="操作" width="68" align="center">
                                <template slot-scope="scope">
                                    <el-button type="primary" @click="selectRule(scope.row)">选择</el-button>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                </el-dialog>`
        })

        Vue.component('mate-search', {
            data() {
                return {
                    isShowPop: false,
                    weightOptions: window._4paradigm_plug_util.arrayRange(-10, 10)
                }
            },
            computed: {
                searchParamCtn() {
                    let count = 0
                    if (
                        this.config.weightRange &&
                        this.config.weightRange.length === 2 &&
                        (this.config.weightRange[0] !== -10 || this.config.weightRange[1] !== 10)
                    ) {
                        count += 1
                    }
                    if (this.config.isTop) {
                        count += 1
                    }
                    if (this.config.isMustPush) {
                        count += 1
                    }
                    return count
                }
            },
            props: {
                config: {},
                isFilter: {
                    type: Boolean,
                    default: true
                }
            },
            methods: {
                showPop() {
                    this.isShowPop = !this.isShowPop
                },
                searchAction() {
                    this.$emit('search', 1)
                },
                toggleChecked(index) {
                    this.config[index] = !this.config[index]
                },
                comfirm() {
                    this.isShowPop = false
                    this.searchAction()
                },
                reset() {
                    this.config.isTop = false
                    this.config.isMustPush = false
                    this.config.weightRange = [-10, 10]
                    this.comfirm()
                },
                submit(e) {
                    // key.Code === 13表示回车键
                    if (e.keyCode === 13) {
                        // 逻辑处理
                        this.searchAction()
                    }
                }
            },
            template: ` <div class="mate-search-scope">
                    <el-input v-model="config.searchContent" :class="isFilter?'paradigm-filter':''" @keyup.enter.native="submit($event)"
                        placeholder="输入标题左前缀或URL或itemId搜索">
                        <template slot="prepend" v-if="isFilter">
                            <div class="mate-search-prepend" :class="isShowPop||searchParamCtn>0?'active':''" @click="showPop">
                                <i class="mate-icon mate-icon-filter"></i>
                                <span>筛选</span>
                            </div>
                        </template>、
                        <i slot="prefix" class="el-input__icon el-icon-search"></i>
                        <span slot="suffix" class="mate-search-suffix" @click="searchAction">搜索</span>
                    </el-input>
                    <div class="mate-search-pop" v-if="isShowPop">
                        <div class="weight-search">
                            <span class="weight-label">权重分</span>
                            <div class="weight-opr">
                                <div class="weight-value">
                                    <div class="weightOptLabelBar">
                                        <span v-for="(item,index) in weightOptions" :key="item" class="weightOptLabel"
                                            :class="index ===0?'first':''">
                                            {{item===config.weightRange[0]||item===config.weightRange[1]?item:''}}
                                        </span>
                                    </div>
                                    <el-slider v-model="config.weightRange" range :show-tooltip='false' :min='-10' :max="10"
                                        class="weight-slider">
                                    </el-slider>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="searchToggleBtn" :class="config.isTop?'active':''" @click="toggleChecked('isTop')">只看置顶</div>
                            <div class="searchToggleBtn" :class="config.isMustPush?'active':''" @click="toggleChecked('isMustPush')">只看必推</div>
                        </div>
                        <div>
                            <el-button type="primary" @click="comfirm" class="searchConfirm">确定</el-button>
                            <el-button @click="reset" class="searchConfirm">重置</el-button>
                        </div>
                    </div>
                </div>`
        })

        Vue.component('update-logs', {
            name: 'UpdateLogs',
            data() {
                return {
                    itemSetId: '',
                    showDialog: false,
                    logsList: []
                }
            },
            props: {},
            computed: {},
            mounted() {},
            methods: {
                show(id) {
                    this.itemSetId = id
                    this.showDialog = true
                },
                hide() {
                    this.showDialog = false
                },
                formatTime(time) {
                    time = new Date(time || 0)
                    let y = time.getFullYear()
                    let m = String(time.getMonth() + 1).padStart(2, '0')
                    let date = String(time.getDate()).padStart(2, '0')
                    return `${y}.${m}.${date}`
                },
                // 获取更新日志列表
                async materialLog() {
                    let {
                        data
                    } = await window._4paradigm_plug_API.materialLog({
                        itemSetId: this.itemSetId || 0
                    })
                    this.logsList = data
                }
            },
            components: {},
            watch: {
                showDialog(val) {
                    if (val) {
                        this.materialLog()
                    }
                }
            },
            template: `<el-dialog title="内容更新日志" :visible.sync="showDialog" width="840px">
                    <div class="update-logs-dialog">
                        <div class="item-box">
                            <el-table empty-text="暂无数据" class="hasTable" :data="logsList" max-height="420" style="width: 100%">
                                <el-table-column width="200" label="操作时间">
                                    <template slot-scope="scope">
                                        {{formatTime(scope.row.opTime)}}
                                    </template>
                                </el-table-column>
                                <el-table-column label="操作类型">
                                    <template slot-scope="scope">
                                        {{scope.row.opTypeDesc}}
                                    </template>
                                </el-table-column>
                                <el-table-column label="更新数据量">
                                    <template slot-scope="scope">
                                        {{scope.row.opCount}}
                                    </template>
                                </el-table-column>
                                <!-- <el-table-column
            label="操作IP">
            <template slot-scope="scope">
              {{scope.row.opIp}}
            </template>
          </el-table-column> -->
                                <!-- <el-table-column
            width="140"
            label="可推荐内容数量">
            <template slot-scope="scope">
              {{scope.row.row}}行
            </template>
          </el-table-column> -->
                                <!-- <el-table-column
            label="有效内容总量">
            <template slot-scope="scope">
              {{scope.row.itemCount}}
            </template>
          </el-table-column> -->
                            </el-table>
                        </div>
                    </div>
                    <div slot="footer" class="dialog-footer">
                        <el-button @click="hide">取消</el-button>
                        <el-button @click="hide" type="primary">确定</el-button>
                    </div>
                </el-dialog>`
        })
        // 批量设置确定弹窗
        Vue.component('mate-batch-confirm', {
            data() {
                return {
                    showAlert: false
                }
            },
            props: {
                title: '',
                memo: ''
            },
            methods: {
                show() {
                    this.showAlert = true
                },
                hide() {
                    this.showAlert = false
                },
                cancel() {
                    this.hide()
                    this.$emit('cancel')
                },
                confirm() {
                    this.hide()
                    this.$emit('confirm')
                }
            },
            template: ` <el-dialog :visible.sync="showAlert" :close-on-click-modal='false' :showClose='true' class="mate-batch-confirm-scope">
                    <div>
                        <div class="mate-confirm-title">{{title}}</div>
                        <div class="mate-confirm-memo">
                            {{memo}}
                        </div>
                    </div>
                    <div slot="footer" class="dialog-footer">
                        <el-button @click="cancel">取消</el-button>
                        <el-button @click="confirm" type="primary">确定</el-button>
                    </div>
                </el-dialog>`
        })
        // 权重设置弹窗
        Vue.component('mate-weight-set', {
            data() {
                return {
                    itemIds: '',
                    oprCtn: 0,
                    weight: {
                        show: false,
                        val: ''
                    },
                    batchMemo: ''
                }
            },
            props: {
                apiParams: {}
            },
            methods: {
                show(ids, val, oprCtn) {
                    this.itemIds = ids
                    this.weight.val = val
                    this.oprCtn = oprCtn
                    this.weight.show = true
                    this.batchMemo = '您即将设置' + this.oprCtn + '条数据的权重分，是否确定?'
                },
                hide() {
                    this.weight.show = false
                },
                // 修改权重
                async editWeight() {
                    if (this.oprCtn > 1) {
                        // 批量操作
                        this.$refs.MateBatchConfirm.show()
                    } else {
                        this.batchConfirm()
                    }
                },
                async batchConfirm() {
                    await window._4paradigm_plug_API.materialRuleOpr(
                        Object.assign({}, this.apiParams, {
                            itemId: this.itemIds,
                            type: mateRuleEnum.WEIGHT,
                            value: this.weight.val,
                            time: ''
                        })
                    )
                    this.weight.show = false
                    this.$emit('refresh-list')
                },
                checkInputNum(item, index) {
                    if (item.val === '' || item.val === undefined) {
                        item.val = 1
                        this.$refs.checkInputNum.setCurrentValue(1)
                    }
                }
            },
            template: ` <div>
                    <el-dialog title="权重设置" :visible.sync="weight.show" class="weight-dialog" :close-on-click-modal='false'>
                        <div>
                            <div class="item-box">
                                <div class="item-title">权重分</div>
                                <el-input-number v-model="weight.val" :min="-10" :max="10" :step="1" @blur="checkInputNum(weight)"
                                    ref="checkInputNum"></el-input-number>
                            </div>
                        </div>
                        <div slot="footer" class="dialog-footer">
                            <el-button @click="weight.show = false">取消</el-button>
                            <el-button type="primary" :disabled="false" @click="editWeight">确定</el-button>
                        </div>
                    </el-dialog>
                    <mate-batch-confirm title='批量设置权重分' :memo='batchMemo' @cancel='hide' @confirm='batchConfirm' ref="MateBatchConfirm"></mate-batch-confirm>
                </div>`
        })

        // 置顶设置弹窗
        Vue.component('mate-top-set', {
            data() {
                return {
                    itemIds: '',
                    // 操作数据数量
                    oprCtn: 0,
                    recom: {
                        show: false,
                        val: 0,
                        time: null
                    },
                    pickerOptions: {
                        // 必推日期禁用状态
                        disabledDate(time, a) {
                            let now = new Date()
                            let y = now.getFullYear()
                            let m = String(now.getMonth() + 1).padStart(2, '0')
                            let date = String(now.getDate()).padStart(2, '0')
                            return time < new Date(`${y}-${m}-${date} 00:00:00`)
                        }
                    },
                    batchMemo: ''
                }
            },
            props: {
                apiParams: {}
            },
            methods: {
                show(ids, val, time, oprCtn) {
                    this.itemIds = ids
                    this.recom.val = val
                    this.recom.time = time
                    this.oprCtn = oprCtn
                    this.recom.show = true
                    this.batchMemo = '您即将设置' + this.oprCtn + '条数据的置顶，是否确定?'
                },
                hide() {
                    this.recom.show = false
                },
                // 修改置顶
                async editRecom() {
                    if (this.oprCtn > 1) {
                        // 批量操作
                        this.$refs.MateBatchConfirm.show()
                    } else {
                        this.batchConfirm()
                    }
                },
                async batchConfirm() {
                    await window._4paradigm_plug_API.materialRuleOpr(
                        Object.assign({}, this.apiParams, {
                            itemId: this.itemIds,
                            type: mateRuleEnum.TOP, // 置顶操作
                            value: this.recom.val,
                            status: this.recom.val,
                            time: this.recom.val === 1 && this.recom.time.length === 2 ?
                                `${this.recom.time[0].replace(/-/g,'')}-${this.recom.time[1].replace(/-/g, '')}` :
                                ''
                        })
                    )
                    this.recom.show = false
                    this.$emit('refresh-list')
                }
            },
            template: ` <div>
                        <el-dialog title="置顶" :visible.sync="recom.show" class="recom-dialog" :close-on-click-modal='false'>
                            <div>
                                <div class="item-box">
                                    <el-radio :label="0" v-model="recom.val">
                                        不置顶
                                    </el-radio>
                                    <br>
                                    <el-radio :label="1" v-model="recom.val">
                                        置顶
                                    </el-radio>
                                    <span style="display: inline-block; width:18px;"></span>
                                    <el-date-picker :editable="false" v-model="recom.time" value-format="yyyy-MM-dd"
                                        type="daterange" range-separator="至" :picker-options="pickerOptions"
                                        start-placeholder="请选择开始日期" end-placeholder="请选择结束日期">
                                    </el-date-picker>
                                    <div class="top-label">注：未设置时间范围将会永久置顶</div>
                                </div>
                            </div>
                            <div slot="footer" class="dialog-footer">
                                <el-button @click="recom.show = false">取消</el-button>
                                <el-button type="primary" @click="editRecom">确定</el-button>
                            </div>
                        </el-dialog>
                        <mate-batch-confirm title='批量设置置顶' :memo='batchMemo' @cancel='hide' @confirm='batchConfirm' ref="MateBatchConfirm"></mate-batch-confirm>
                    </div>`
        })

        // 必推设置弹窗
        Vue.component('mate-recom-set', {
            data() {
                return {
                    itemIds: '',
                    // 操作数据数量
                    oprCtn: 0,
                    recom: {
                        show: false,
                        val: 1,
                        time: []
                    },
                    pickerOptions: {
                        // 必推日期禁用状态
                        disabledDate(time, a) {
                            let now = new Date()
                            let y = now.getFullYear()
                            let m = String(now.getMonth() + 1).padStart(2, '0')
                            let date = String(now.getDate()).padStart(2, '0')
                            return time < new Date(`${y}-${m}-${date} 00:00:00`)
                        }
                    },
                    batchMemo: ''
                }
            },
            props: {
                apiParams: {}
            },
            methods: {
                show(ids, val, time, oprCtn) {
                    this.itemIds = ids
                    this.recom.val = val
                    this.recom.time = time
                    this.oprCtn = oprCtn
                    this.recom.show = true
                    this.batchMemo = '您即将设置' + this.oprCtn + '条数据的必推时效，是否确定?'
                },
                hide() {
                    this.recom.show = false
                },
                // 修改必推
                async editRecom() {
                    if (this.oprCtn > 1) {
                        // 批量操作
                        this.$refs.MateBatchConfirm.show()
                    } else {
                        this.batchConfirm()
                    }
                },
                async batchConfirm() {
                    await window._4paradigm_plug_API.materialRuleOpr(
                        Object.assign({}, this.apiParams, {
                            itemId: this.itemIds,
                            type: mateRuleEnum.PUSH,
                            value: this.recom.val,
                            status: this.recom.val,
                            time: this.recom.val === 1 && this.recom.time.length === 2 ?
                                `${this.recom.time[0].replace( /-/g,'')}-${this.recom.time[1].replace(/-/g, '')}` :
                                ''
                        })
                    )
                    this.recom.show = false
                    this.$emit('refresh-list')
                }
            },
            template: ` <div>
                            <el-dialog title="必推" :visible.sync="recom.show" class="recom-dialog" :close-on-click-modal='false'>
                                <div>
                                    <div class="item-box">
                                        <el-radio :label="0" v-model="recom.val">
                                            非必推
                                        </el-radio>
                                        <br>
                                        <el-radio :label="1" v-model="recom.val">
                                            必推
                                        </el-radio>
                                        <span style="display: inline-block; width:18px;"></span>
                                        <el-date-picker :editable="false" v-model="recom.time" value-format="yyyy-MM-dd"
                                            type="daterange" range-separator="至" :picker-options="pickerOptions"
                                            start-placeholder="请选择开始日期" end-placeholder="请选择结束日期">
                                        </el-date-picker>
                                    </div>
                                </div>
                                <div slot="footer" class="dialog-footer">
                                    <el-button @click="recom.show = false">取消</el-button>
                                    <el-button type="primary" :disabled="recom.val !== 0 && recom.time.length===0"
                                        @click="editRecom">确定</el-button>
                                </div>
                            </el-dialog>
                            <mate-batch-confirm title='批量设置必推时效' :memo='batchMemo' @cancel='hide' @confirm='batchConfirm'
                                ref="MateBatchConfirm"></mate-batch-confirm>
                        </div> `
        })

        Vue.component('mate-opr-ctn-alert', {
            data() {
                return {
                    showAlert: false,
                    itemSetId: ''
                }
            },
            methods: {
                show(id) {
                    this.showAlert = true
                    this.itemSetId = id
                }
            },
            template: ` 
            <el-dialog title="编辑内容过多" :visible.sync="showAlert" :show-close='false' :close-on-click-modal='false' class="mate-ctn-alert-scope">
                <div>
                <i class="mate-alert-icon el-icon-warning"></i>
                <div class="mate-alert-memo">
                    <p>编辑的内容超过4000条会导致场景不可用。</p>
                    <p>请减少选择内容数量。</p>
                </div>
                </div>
                <div slot="footer" class="dialog-footer">
                <el-button @click="showAlert = false" type="primary">确定</el-button>
                </div>
            </el-dialog>
            `
        })

        Vue.component('dynamic-table', {
            name: 'DynamicTable',
            props: {
                headers: {
                    type: Array,
                    required: true
                },
                dataList: {
                    type: Array,
                    required: true
                }
            },
            data() {
                return {}
            },
            template: `<el-table class="dynamic-table" border :data="dataList">
                            <el-table-column align="center" v-for="header in headers" :key="header.label" :label="header.label"
                                :width="header.width">
                                <template slot-scope="scope">
                                    {{ dataList[scope.$index][header.prop] }}
                                </template>
                            </el-table-column>
                        </el-table>`
        })
        // header配置
        // 1. 加权项曝光分布
        const weightItemHeaders = [{
                label: '权重分',
                prop: 'weight'
            },
            {
                label: '文章数',
                prop: 'itemCount'
            },
            {
                label: '曝光数',
                prop: 'showPV'
            }
        ]
        // 2. 必推物料曝光分布，和3完全一致
        const appearItemHeaders = [{
                label: '物料id',
                prop: 'itemID'
            },
            {
                label: 'url',
                prop: 'url'
            },
            {
                label: '曝光量',
                prop: 'showPV',
                width: 120
            },
            {
                label: '点击量',
                prop: 'detailPageShowPV',
                width: 120
            },
            {
                label: '点击率',
                prop: 'ctr',
                width: 120
            }
        ]
        // 3. 置顶物料曝光分布
        const topItemHeaders = [{
                label: '物料id',
                prop: 'itemID'
            },
            {
                label: 'url',
                prop: 'url'
            },
            {
                label: '曝光量',
                prop: 'showPV',
                width: 120
            },
            {
                label: '点击量',
                prop: 'detailPageShowPV',
                width: 120
            },
            {
                label: '点击率',
                prop: 'ctr',
                width: 120
            }
        ]
        const ItemTypeEnum = {
            WEIGHT: 'WEIGHT',
            APPEAR: 'APPEAR',
            TOP: 'TOP'
        }
        const itemTypeTabs = [{
                name: ItemTypeEnum.WEIGHT,
                label: '加权项曝光分布'
            },
            {
                name: ItemTypeEnum.APPEAR,
                label: '必推物料曝光分布'
            },
            {
                name: ItemTypeEnum.TOP,
                label: '置顶物料曝光分布'
            }
        ]
        const HeadersMap = {
            [ItemTypeEnum.WEIGHT]: weightItemHeaders,
            [ItemTypeEnum.APPEAR]: appearItemHeaders,
            [ItemTypeEnum.TOP]: topItemHeaders
        }
        Vue.component('operation-data-list', {
            name: 'OperationDataList',
            data() {
                return {
                    title: '操作数据',
                    itemSetId: '',
                    showDialog: false,
                    selectedDate: null,
                    // 用户ID与场景ID
                    customID: null,
                    sceneID: null,
                    // 选项卡的配置数据
                    activeItemType: ItemTypeEnum.WEIGHT,
                    itemTypeTabs,
                    // 数据
                    weightItemDataList: [],
                    appearItemDataList: [],
                    topItemDataList: [],
                    sceneShowUV: 0,
                    datePickerOptions: {
                        disabledDate(time) {
                            return time.getTime() > Date.now()
                        }
                    }
                }
            },
            props: {},
            computed: {
                headers() {
                    return HeadersMap[this.activeItemType]
                },
                dataList() {
                    const activeItemType = this.activeItemType
                    if (activeItemType === ItemTypeEnum.WEIGHT) return this.weightItemDataList
                    if (activeItemType === ItemTypeEnum.APPEAR) return this.appearItemDataList
                    if (activeItemType === ItemTypeEnum.TOP) return this.topItemDataList
                },
                formatedSelectedDate() {
                    return window._4paradigm_plug_util.formatDate(this.selectedDate)
                }
            },
            mounted() {
                this.init()
            },
            methods: {
                // 测试数据
                // customID = 17
                // sceneID = 74
                async init() {
                    // 拉取一次用户数据。。。这很苟且
                    const {
                        data
                    } = await window._4paradigm_plug_API.getUserInfo()
                    this.customID = data.id
                    this.sceneID = querySceneId
                    // 默认选择时间为昨天！
                    this.selectedDate = window._4paradigm_plug_util.getDate(-1)
                    this.getDataList()
                    this.getScenetShowUV()
                },
                show(itemSetId, sceneID) {
                    this.itemSetId = itemSetId
                    this.sceneID = sceneID
                    this.showDialog = true
                },
                hide() {
                    this.showDialog = false
                },
                async getDataList() {
                    const [itemType, date] = [this.activeItemType, this.formatedSelectedDate]
                    const {
                        customID,
                        sceneID
                    } = this
                    const params = {
                        customID,
                        sceneID,
                        begin: date,
                        end: date
                    }
                    // customID 客户ID
                    // sceneID 场景ID
                    let data
                    if (itemType === ItemTypeEnum.WEIGHT) {
                        data = await window._4paradigm_plug_API.getReportByItemWeight(params)
                        this.weightItemDataList = data.data ? JSON.parse(data.data) : []
                    } else if (itemType === ItemTypeEnum.APPEAR) {
                        data = await window._4paradigm_plug_API.getReportByItemAppear(params)
                        this.appearItemDataList = data.data ? JSON.parse(data.data) : []
                    } else if (itemType === ItemTypeEnum.TOP) {
                        data = await window._4paradigm_plug_API.getReportByItemTop(params)
                        this.topItemDataList = data.data ? JSON.parse(data.data) : []
                    }
                },
                async getScenetShowUV() {
                    const {
                        customID,
                        sceneID
                    } = this
                    const date = this.formatedSelectedDate
                    const params = {
                        customID,
                        sceneID,
                        begin: date,
                        end: date
                    }
                    const data = await window._4paradigm_plug_API.getReportSceneShowUV(params)
                    let sceneShowUV = 0
                    try {
                        const sceneShowDataList = JSON.parse(data.data)
                        const sceneShowData = sceneShowDataList[0]
                        if (sceneShowData) sceneShowUV = sceneShowData.showUV
                    } catch (e) {
                        console.error(e)
                    }
                    this.sceneShowUV = sceneShowUV || 0
                },
                selectItemType(tab, event) {
                    this.getDataList()
                },
                selectDate(date) {
                    this.getDataList()
                    this.getScenetShowUV()
                }
            },
            components: {

            },
            watch: {
                showDialog(val) {
                    if (val) {
                        // this.materialLog()
                    }
                }
            },
            template: ` <el-dialog :title="title" class="operation-data-list" :visible.sync="showDialog"
                            width="900px">
                            <div class="report-header">
                                <!-- 日期选择器用来指定选择哪一天的数据 -->
                                <el-date-picker type="date" @change="selectDate" v-model="selectedDate" :picker-options="datePickerOptions"
                                    placeholder="选择一个日期">
                                </el-date-picker>
                                <!-- 显示场景uv -->
                                <span class="txt-scene-uv">
                                    场景总UV：{{ sceneShowUV }}
                                </span>
                            </div>
                            <!-- 选项卡用来选择要显示的数据项 -->
                            <div class="el-tabs-wrapper">
                                <el-tabs v-model="activeItemType" @tab-click="selectItemType">
                                    <el-tab-pane v-for="tab in itemTypeTabs" :key="tab.name" :label="tab.label" :name="tab.name">
                                    </el-tab-pane>
                                </el-tabs>
                            </div>
                            <!-- /选项卡用来选择要显示的数据项 -->
                            <!-- 数据展示区域 -->
                            <div class="operation-data-dialog">
                                <div class="item-box">
                                    <dynamic-table :headers="headers" :data-list="dataList">
                                    </dynamic-table>
                                </div>
                            </div>
                            <!-- /数据展示区域 -->
                            <div slot="footer" class="dialog-footer">
                                <!-- /日期选择器用来指定选择哪一天的数据 -->
                                <el-button @click="hide">取消</el-button>
                                <el-button @click="hide" type="primary">确定</el-button>
                            </div>
                        </el-dialog>`
        })



        new Vue({
            el: '#paradigm_mate_page',
            data() {
                return {
                    sceneId: querySceneId,
                    itemSetId: queryItemSetId,
                    itemSetName: "",
                    materialsList: [],
                    searchConfig: {
                        searchContent: "",
                        isTop: false,
                        isMustPush: false,
                        weightRange: [-10, 10]
                    },
                    columns: [],
                    currentPage: 1,
                    pageSize: 20,
                    totalPages: 0,
                    // 表头字段对应中文
                    theadTitle: {
                        tag: "内容标签",
                        url: "内容地址",
                        coverUrl: "封面图地址"
                    },
                    columnWidth: {
                        tag: "180",
                        url: "300",
                        coverUrl: "300"
                    },
                    selectItemMap: new Map(),
                    selectItemCount: 0,
                    tableMaxHeight: 200,
                    isEsSearch: false,
                    goBackStyle: {},
                    sortParam: {},
                    mateRuleEnum: mateRuleEnum,
                    // 显示回到最左侧的按钮
                    showScrollBtn: false,
                    interval: "",
                    apiParams: {
                        sceneId: querySceneId,
                        itemSetId: queryItemSetId,
                    },
                    /****************过滤规则 start ****** */
                    // 过滤规则对象
                    filterRule: {
                        id: 0,
                        sceneId: querySceneId ? Number(querySceneId) : 0,
                        itemSetId: queryItemSetId,
                        title: "",
                        // 是否显示过滤规则
                        isShow: false,
                        // 是否编辑过滤规则状态
                        isEdit: false,
                        // 符合的内容条数
                        itemNumber: 0,
                        categoryV1s: [],
                        // 内容时效，默认为全部推荐
                        mateTime: -1,
                        timeValue: 7,
                        // 内容类型，默认为0，全部类型
                        coverUrlCnt: -1
                    },
                    initFlg: true,
                    limitNum: 100
                    /****************过滤规则 end ****** */
                };
            },
            mounted() {
                // 设置table的最大高度
                this.setTableMaxHeight();
                // 窗口大小变更，重新计算table的最大高度
                window._4paradigm_plug_util.bindResizeWindow(this.setTableMaxHeight);
                this.interval = setInterval(() => {
                    this.setScrollLeftBtn();
                }, 200);
                this.initRuleData()
            },
            beforeDestroy() {
                window._4paradigm_plug_util.unbindResizeWindow();
                clearInterval(this.interval);
            },
            methods: {
                logoClick() {
                    let phpUrl = window.location.origin + window.location.pathname;
                    window.location.href = phpUrl +
                        '?page=rec_xianjian_rec_options'
                },
                // 每一行点击checkbox的回调
                tableItemSelect(selection, row) {
                    this.setSelectItemMap(selection.indexOf(row) > -1, row);
                },
                // 点击全选的回调
                tableSelectAll(selection) {
                    this.materialsList.forEach(row => {
                        this.setSelectItemMap(selection && selection.length > 0, row);
                    });
                },
                // 记录分页选中数据map
                setSelectItemMap(setFlg, row) {
                    if (setFlg) {
                        this.selectItemMap.set(row.id, row);
                    } else {
                        if (this.selectItemMap.has(row.id)) {
                            this.selectItemMap.delete(row.id);
                        }
                    }
                    this.selectItemCount = this.selectItemMap.size;
                },
                // 排序回调
                sortChange(value) {
                    this.sortParam = value;
                    if (!this.isEsSearch) {
                        // 非关键词搜索模式
                        this.materisalDetails();
                    }
                },
                getAlignByCol(col) {
                    if (col === "categoryId" || col === "publisherId" || col === "tag") {
                        return "left";
                    }
                },
                setTableMaxHeight() {
                    const content = this.$refs.materialsDetailContent;
                    if (content) {
                        const tableHeight = content.offsetHeight - 220;
                        this.tableMaxHeight = tableHeight > 200 ? tableHeight : 200;
                        this.setGoBackStyle();
                    }
                },
                setGoBackStyle() {
                    this.$nextTick(() => {
                        const pageScope = document.getElementById("materials-detail-scope");
                        if (!pageScope) {
                            return
                        }
                        const table = pageScope.getElementsByClassName("el-table")[0];
                        if (table) {
                            this.goBackStyle = {
                                "margin-top": table.offsetHeight / 2 + "px"
                            };
                        }
                    });
                },
                getColValue(item) {
                    // 点击率
                    if (item["item4pdShowCnt"] && item["item4pdClickCnt"]) {
                        const point = item["item4pdClickCnt"] / item["item4pdShowCnt"];
                        return Number(point * 100).toFixed(3) + "%";
                    }
                    return "--";
                },
                showInput(item, col) {
                    this.hideAllCopy();
                    if (col !== "url") {
                        return;
                    }
                    this.$set(item, "copyFlg", true);
                },
                hideAllCopy() {
                    for (let model of this.materialsList) {
                        this.$set(model, "copyFlg", false);
                    }
                },
                refreshList() {
                    this.selectItemMap.clear();
                    this.selectItemCount = 0;
                    this.materisalDetails();
                },
                searchList() {
                    this.currentPage = 1;
                    if (this.$refs.mateTable) {
                        this.$refs.mateTable.clearSort();
                    }
                    this.refreshList();
                },
                formatTime(ctime) {
                    let time = new Date(ctime || 0);
                    let y = time.getFullYear();
                    let m = String(time.getMonth() + 1).padStart(2, "0");
                    let date = String(time.getDate()).padStart(2, "0");
                    return `${y}.${m}.${date}`;
                },
                optTip(type, isBatch) {
                    if (isBatch) {
                        this.$message.error(
                            `选中的内容包含已经封禁的内容，无法${type}，如需${type}，请先行解禁内容`
                        );
                    } else {
                        this.$message.error(
                            `此内容已经封禁无法${type}，如需${type}，请先行解禁内容`
                        );
                    }
                },
                // 更新报表下拉菜单的处理
                handleViewReportCommand(command) {
                    if (command === "viewLogs") this.viewLogs();
                    if (command === "viewOperationDataList") this.viewOperationDataList();
                },
                // 查看更新日志
                viewLogs() {
                    this.$refs.UpdateLogs.show(this.itemSetId);
                },
                // 查看操作数据
                viewOperationDataList() {
                    this.$refs.OperationDataList.show(this.itemSetId, this.sceneId);
                },
                // 获取内容详情
                async materisalDetails() {
                    this.searchConfig.searchContent = this.searchConfig.searchContent.trim();
                    let orderbyColumn, orderby, weightBegin, weightEnd;
                    if (this.searchConfig.searchContent.trim()) {
                        // es查询
                        orderbyColumn = "";
                        orderby = "";
                    } else {
                        orderbyColumn = this.sortParam.prop === "publishTime" ? 2 : 1;
                        orderby = this.sortParam.order === "ascending" ? 2 : 1;
                    }
                    if (
                        this.searchConfig.weightRange[0] === -10 &&
                        this.searchConfig.weightRange[1] === 10
                    ) {
                        weightBegin = "";
                        weightEnd = "";
                    } else {
                        weightBegin = this.searchConfig.weightRange[0];
                        weightEnd = this.searchConfig.weightRange[1];
                    }
                    let params = Object.assign({}, this.apiParams, {
                        page: this.currentPage,
                        pageSize: this.pageSize,
                        content: this.searchConfig.searchContent,
                        top: this.searchConfig.isTop ? 1 : 0,
                        push: this.searchConfig.isMustPush ? 1 : 0,
                        weightBegin: weightBegin,
                        weightEnd: weightEnd,
                        orderbyColumn: orderbyColumn,
                        orderby: orderby,
                        categoryV1s: this.filterRule.id ? this.filterRule.categoryV1s + "" : "",
                        publishTime: this.filterRule.mateTime === 1 ?
                            this.filterRule.timeValue : this.filterRule.mateTime,
                        coverUrlCnt: this.filterRule.coverUrlCnt
                    });
                    let {
                        data,
                        code
                    } = await window._4paradigm_plug_API.getSceneMaterisalDetail(params);
                    if (code === 200) {
                        if (!data.items) {
                            data.items = [];
                        }
                        this.materialsList = data.items;
                        this.$nextTick(function() {
                            this.materialsList.forEach(row => {
                                if (this.selectItemMap.has(row.id)) {
                                    this.$refs.mateTable.toggleRowSelection(row, true);
                                }
                            });
                        });
                        this.itemSetName = data.itemSetName;
                        this.totalPages = data.allPage;
                        this.currentPage = data.page;
                        this.columns = ["tag", "url", "coverUrl"];
                        if (this.searchConfig.searchContent.trim()) {
                            // es查询
                            this.isEsSearch = true;
                        } else {
                            this.isEsSearch = false;
                        }

                        this.setGoBackStyle();
                    }
                },
                // 当前页改变时的回调函数
                handleCurrentChange(curPage) {
                    this.currentPage = curPage;
                    this.materisalDetails();
                },
                // 每页显示条数改变的回调
                handleSizeChange(pageSize) {
                    this.currentPage = 1;
                    this.pageSize = pageSize;
                    this.materisalDetails();
                },
                // 显示编辑权重弹窗
                showWeight(item) {
                    if (item.isRecommend === 0) {
                        this.optTip("修改权重");
                        return;
                    }
                    const weightObj =
                        item.sceneItemRuleMap && item.sceneItemRuleMap[mateRuleEnum.WEIGHT];
                    const weight = weightObj ? weightObj.value : 1;
                    this.$refs.MateWeightSet.show(item.itemId, weight, 0);
                },
                // 显示必推弹窗
                showPush(item) {
                    if (item.isRecommend === 0) {
                        this.optTip("修改必推时效");
                        return;
                    }
                    // 必推时效，默认配置
                    const pushObj = (item.sceneItemRuleMap &&
                        item.sceneItemRuleMap[mateRuleEnum.PUSH]) || {
                        value: 1
                    };
                    const push = this.getRuleValue(item, mateRuleEnum.PUSH) || 0;
                    const time =
                        push === 1 ? [
                            this.formatTime(pushObj.beginTime * 1000),
                            this.formatTime(pushObj.endTime * 1000)
                        ] : [];
                    this.$refs.MateRecomSet.show(item.itemId, push, time, 0);
                },
                // 显示置顶弹窗
                showMaterialTop(item) {
                    if (item.isRecommend === 0) {
                        this.optTip("进行置顶操作");
                        return;
                    }
                    // 置顶，默认配置
                    const topObj = (item.sceneItemRuleMap &&
                        item.sceneItemRuleMap[mateRuleEnum.TOP]) || {
                        value: 1
                    };
                    const top = this.getRuleValue(item, mateRuleEnum.TOP) || 0;
                    let time = [];
                    if (top === 1 && topObj.beginTime && topObj.endTime) {
                        time = [
                            this.formatTime(topObj.beginTime * 1000),
                            this.formatTime(topObj.endTime * 1000)
                        ];
                    }
                    this.$refs.MateTopSet.show(item.itemId, top, time, 0);
                },
                // 批量设置权重分
                async batchWeightSet() {
                    const {
                        ids,
                        weight
                    } = await this.getSelectOprDefaultConfig("修改权重");
                    if (ids) {
                        this.$refs.MateWeightSet.show(ids, weight, this.selectItemCount);
                    }
                },
                // 批量设置必推时效
                async batchPushSet() {
                    const {
                        ids,
                        push,
                        pushTime
                    } = await this.getSelectOprDefaultConfig(
                        "修改必推时效"
                    );
                    if (ids) {
                        this.$refs.MateRecomSet.show(ids, push, pushTime, this.selectItemCount);
                    }
                },
                // 批量设置置顶
                async batchTopSet() {
                    const {
                        ids,
                        top,
                        topTime
                    } = await this.getSelectOprDefaultConfig(
                        "进行置顶操作"
                    );
                    if (ids) {
                        this.$refs.MateTopSet.show(ids, top, topTime, this.selectItemCount);
                    }
                },
                // 获取操作的对象id
                async getSelectOprDefaultConfig(type) {
                    let config = {
                        ids: "",
                        weight: 10,
                        push: 0,
                        pushTime: ["", ""],
                        top: 0,
                        topTime: ["", ""]
                    };
                    if (this.selectItemMap.size > 4000) {
                        this.$refs.MateOprCtnAlert.show(this.itemSetId);
                        return config;
                    }
                    let notRecommend = false;
                    this.selectItemMap.forEach(item => {
                        if (item.isRecommend === 0) {
                            this.optTip(type, true);
                            notRecommend = true;
                        }
                        // 默认权重为所有权重中的最小值
                        const weightObj =
                            item.sceneItemRuleMap && item.sceneItemRuleMap[mateRuleEnum.WEIGHT];
                        const weight = weightObj ? weightObj.value : 1;
                        config.weight = Math.min(config.weight, weight);
                        // 必推时效，默认配置
                        this.getPushOprConfig(item, config);
                        // 置顶，默认配置
                        this.getTOPOprConfig(item, config);
                        config.ids += item.itemId + ",";
                    });
                    if (config.pushTime.length === 2) {
                        const beginTime = config.pushTime[0];
                        const endTime = config.pushTime[1];
                        config.pushTime = [
                            this.formatTime(beginTime * 1000),
                            this.formatTime(endTime * 1000)
                        ];
                    }
                    if (config.topTime.length === 2) {
                        const beginTime = config.topTime[0];
                        const endTime = config.topTime[1];
                        config.topTime = [
                            this.formatTime(beginTime * 1000),
                            this.formatTime(endTime * 1000)
                        ];
                    }
                    if (notRecommend) {
                        return "";
                    }
                    let {
                        data
                    } = await window._4paradigm_plug_API.getMatesOprCanSave(
                        Object.assign({}, this.apiParams, {
                            itemIds: config.ids
                        })
                    );
                    if (data && data.canSave) {
                        return config;
                    } else {
                        this.$refs.MateOprCtnAlert.show(this.itemSetId);
                        config.ids = "";
                        return config;
                    }
                },
                // 获取批量必推的默认值
                getPushOprConfig(item, config) {
                    const pushObj = (item.sceneItemRuleMap &&
                        item.sceneItemRuleMap[mateRuleEnum.PUSH]) || {
                        value: 0
                    };
                    config.push = Math.max(config.push, pushObj.value);
                    if (config.pushTime.length === 2) {
                        // 可以设置值的
                        if (config.pushTime[0] === "" || config.pushTime[1] === "") {
                            // 遍历第一项
                            if (pushObj.beginTime && pushObj.endTime) {
                                config.pushTime = [pushObj.beginTime, pushObj.endTime];
                            } else {
                                // 存在无时间设置的项，将时间设置为空的
                                config.pushTime = [];
                            }
                        } else {
                            // 说明前面的项目已经设置时间了
                            if (
                                config.pushTime[0] !== pushObj.beginTime ||
                                config.pushTime[1] !== pushObj.endTime
                            ) {
                                // 存在项目与之前的项目时间不一致
                                config.pushTime = [];
                            }
                        }
                    }
                },
                // 获取批量必推的默认值
                getTOPOprConfig(item, config) {
                    const topObj = (item.sceneItemRuleMap &&
                        item.sceneItemRuleMap[mateRuleEnum.TOP]) || {
                        value: 0
                    };
                    config.top = Math.max(config.top, topObj.value);
                    if (config.topTime.length === 2) {
                        // 可以设置值的
                        if (config.topTime[0] === "" || config.topTime[1] === "") {
                            // 遍历第一项
                            if (topObj.beginTime && topObj.endTime) {
                                config.topTime = [topObj.beginTime, topObj.endTime];
                            } else {
                                // 存在无时间设置的项，将时间设置为空的
                                config.topTime = [];
                            }
                        } else {
                            // 说明前面的项目已经设置时间了
                            if (
                                config.topTime[0] !== topObj.beginTime ||
                                config.topTime[1] !== topObj.endTime
                            ) {
                                // 存在项目与之前的项目时间不一致
                                config.topTime = [];
                            }
                        }
                    }
                },
                // 获取规则的值
                getRuleValue(item, index) {
                    return (
                        item.sceneItemRuleMap &&
                        item.sceneItemRuleMap[index] &&
                        item.sceneItemRuleMap[index].value
                    );
                },
                getTopTimeShow(item) {
                    const beginTime = item.sceneItemRuleMap[mateRuleEnum.TOP].beginTime;
                    const endTime = item.sceneItemRuleMap[mateRuleEnum.TOP].endTime;
                    if (beginTime && endTime) {
                        return (
                            this.formatTime(beginTime * 1000) +
                            "—" +
                            this.formatTime(endTime * 1000)
                        );
                    } else {
                        return "已置顶";
                    }
                },
                viewSet(data) {
                    // 显示设置
                    this.$refs.MateShowSetDialog.show(data);
                },
                // 横向滚动条滚到最初位置
                tableScrollLeft() {
                    const table = document.getElementById("materials-detail-scope");
                    if (!table) {
                        return
                    }
                    const tableScrollXDiv = table.getElementsByClassName(
                        "el-table__body-wrapper"
                    );
                    if (tableScrollXDiv && tableScrollXDiv.length > 0) {
                        tableScrollXDiv[0].scrollLeft = 0;
                    }
                },
                // 判断横向滚动条的位置
                setScrollLeftBtn() {
                    const table = document.getElementById("materials-detail-scope");
                    if (!table) {
                        return
                    }
                    const tableScrollXDiv = table.getElementsByClassName(
                        "el-table__body-wrapper"
                    );
                    if (tableScrollXDiv && tableScrollXDiv.length > 0) {
                        if (tableScrollXDiv[0].scrollLeft) {
                            this.showScrollBtn = true;
                        } else {
                            this.showScrollBtn = false;
                        }
                    }
                },
                // 修改禁封
                async editBan(isRecommend, itemIds) {
                    await window._4paradigm_plug_API.materialBan({
                        itemSetId: queryItemSetId,
                        itemIds,
                        isRecommend: isRecommend ? 1 : 0
                    })
                    this.refreshList()
                },
                // 添加过滤规则
                addFilterRule() {
                    this.filterRule.isShow = true;
                    this.filterRule.isEdit = true;
                },
                /*********************************过滤规则start******* */
                async initRuleData() {
                    await this.getFilterRule()
                    await this.getMateCategoryV1()
                },
                // 获取过滤规则
                async getFilterRule() {
                    let {
                        data
                    } = await window._4paradigm_plug_API.getFilterRule({
                        sceneId: this.filterRule.sceneId
                    })
                    if (data) {
                        this.filterRule.title = data.sceneName
                        if (data.selectionRule) {
                            this.filterRule.isShow = true
                            const selectionRule = data.selectionRule
                            this.filterRule.id = selectionRule.id
                            this.filterRule.coverUrlCnt = selectionRule.coverUrlCnt
                            this.filterRule.itemNumber = selectionRule.itemNumber
                            if (selectionRule.publishTime === -1) {
                                this.filterRule.mateTime = -1
                                this.filterRule.timeValue = 7
                            } else {
                                this.filterRule.mateTime = 1
                                this.filterRule.timeValue = selectionRule.publishTime
                            }
                        }
                        if (data.categorys) {
                            this.filterRule.categoryV1s = data.categorys
                        }
                    }
                    this.refreshList()
                },
                // 获取全部一级分类
                async getMateCategoryV1() {
                    let {
                        data
                    } = await window._4paradigm_plug_API.getMateCategoryV1({
                        itemSetId: this.filterRule.itemSetId
                    })
                    const categoryV1List = data
                    if (this.filterRule.categoryV1s.length === 0) {
                        this.filterRule.categoryV1s = categoryV1List
                    }
                    const allCategoryV1sList = []
                    categoryV1List.forEach(item => {
                        const v1Obj = {
                            name: item,
                            checked: false
                        }
                        if (this.filterRule.categoryV1s.indexOf(item) > -1) {
                            v1Obj.checked = true
                        }
                        allCategoryV1sList.push(v1Obj)
                    })
                    this.filterRule.allCategoryV1sList = allCategoryV1sList
                    if (this.initFlg) this.initFlg = false
                },
                editRule() {
                    this.filterRule.isEdit = true
                },
                deleteRule() {
                    this.$confirmEditRule('确认删除过滤规则?', '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'error'
                        })
                        .then(() => {
                            removeFilterRule({
                                sceneId: this.filterRule.sceneId
                            }).then(({
                                code,
                                info
                            }) => {
                                if (code === 200) {
                                    this.filterRule.isShow = false
                                    this.filterRule.categoryV1s = []
                                    // 内容时效，默认为全部推荐
                                    this.filterRule.mateTime = -1
                                    this.filterRule.timeValue = 7
                                    // 内容类型，默认为0，全部类型
                                    this.filterRule.coverUrlCnt = -1
                                    this.filterRule.id = ''
                                    this.$emit('initRuleData')
                                    this.$message({
                                        type: 'success',
                                        message: '删除成功!',
                                        duration: 1500
                                    })
                                } else {
                                    this.$message.error(info)
                                }
                            })
                        })
                        .catch(() => {})
                },
                // 在已有规则中选择
                checkFromExit() {
                    this.$refs.RuleExistSelect.show(this.filterRule.sceneId)
                },
                // 选中已有规则
                checkExist(rule) {
                    this.filterRule.coverUrlCnt = rule.selectionRule.coverUrlCnt
                    if (rule.selectionRule.publishTime === -1) {
                        this.filterRule.mateTime = -1
                        this.filterRule.timeValue = 7
                    } else {
                        this.filterRule.mateTime = 1
                        this.filterRule.timeValue = rule.selectionRule.publishTime
                    }
                    this.filterRule.categoryV1s = rule.categorys
                },
                hideEditRule() {
                    this.filterRule.isEdit = false
                    if (!this.filterRule.id) {
                        this.filterRule.isShow = false
                    }
                    this.filterRule.categoryV1s = []
                    // 内容时效，默认为全部推荐
                    this.filterRule.mateTime = -1
                    this.filterRule.timeValue = 7
                    // 内容类型，默认为0，全部类型
                    this.filterRule.coverUrlCnt = -1
                    this.$emit('initRuleData')
                },
                // 保存
                async confirmEditRule() {
                    const param = {
                        sceneId: this.filterRule.sceneId,
                        categoryV1s: this.filterRule.categoryV1s,
                        publishTime: this.filterRule.mateTime === -1 ?
                            this.filterRule.mateTime : this.filterRule.timeValue,
                        coverUrlCnt: this.filterRule.coverUrlCnt,
                        itemNumber: this.filterRule.itemNumber
                    }
                    if (this.filterRule.id) {
                        param.id = this.filterRule.id
                    }
                    let {
                        code,
                        info
                    } = await window._4paradigm_plug_API.saveFilterRule(param)
                    if (code !== 200) {
                        this.$message.error(info)
                        return
                    }
                    this.$emit('initRuleData')
                    this.filterRule.isEdit = false
                },
                // 选择类别
                selectCategory() {
                    this.filterRule.allCategoryV1sList.forEach(item => {
                        item.checked = false
                        if (this.filterRule.categoryV1s.indexOf(item.name) > -1) {
                            item.checked = true
                        }
                    })
                    this.$refs.RuleTagSelect.show(this.filterRule.allCategoryV1sList)
                },
                // 确认选择类别
                tagSelectConfirm(allCategoryV1sList) {
                    this.filterRule.categoryV1s = []
                    allCategoryV1sList.forEach(item => {
                        if (item.checked) {
                            this.filterRule.categoryV1s.push(item.name)
                        }
                    })
                },
                // 获取符合规则条数
                async getFilterItemNumber() {
                    const param = {
                        sceneId: this.filterRule.sceneId,
                        categoryV1s: this.filterRule.categoryV1s || [],
                        publishTime: this.filterRule.mateTime === -1 ?
                            this.filterRule.mateTime : this.filterRule.timeValue,
                        coverUrlCnt: this.filterRule.coverUrlCnt
                    }
                    let {
                        data
                    } = await window._4paradigm_plug_API.getFilterItemNumber(param)
                    this.filterRule.itemNumber = data
                }
                /*********************************过滤规则end******* */
            },
            watch: {
                filterRule: {
                    handler(curVal) {
                        // 获取符合规则条数
                        this.getFilterItemNumber()
                    },
                    deep: true
                }
            }

        });
    </script>

</body>

</html>