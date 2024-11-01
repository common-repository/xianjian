<html lang="zh">

<head>
    <meta charset="UTF-8">
    <title>先荐--场景列表</title>
    <?php include 'config_common.php' ?>
    <?php include 'nav.php' ?>
    <?php include 'adsList.php' ?>
</head>
<body class="paradigm_body">
<div id='paradigm_scene_list' class='paradigm-app' v-cloak>
    <div class='_paradigm-content-plugIn'>
        <paradigm-nav @logo-click='logoClick'></paradigm-nav>
        <div class='_paradigm-list-tab'>
            <div @click='swichListTab("SCENE_LIST")'
                 :class='showTabIndex=== "SCENE_LIST"?"_paradigm-list-tab-item active":"_paradigm-list-tab-item"'>内容推荐设置
            </div>
            <div @click='swichListTab("ADS_LIST")'
                 :class='showTabIndex=== "ADS_LIST"?"_paradigm-list-tab-item active":"_paradigm-list-tab-item"'>广告收益
            </div>
        </div>
        <div class="_paradigm-content-plugIn-router" v-if='showTabIndex=== "SCENE_LIST"'>
            <div class="_paradigm-plug-scene-list">
                <div class="_paradigm-main-title">
                    <span class="memo">共有{{sceneList.length}}个推荐栏</span>
                    <el-button type='primary' @click="showAddScene">
                        新建推荐栏
                    </el-button>
                </div>
                <div v-for="(item,index) in sceneList" :key="index" class="_paradigm-plug-scence-list-item">
                    <div class="title-bar">
                        <span class="title">{{item.name}}</span>
                        <el-tag :type="getTagStyle(item)">{{getCurrentStatus(item)}}</el-tag>
                        <!-- <span class="wait-online-memo" v-if="item.status === statusSteps.step3_4 || item.status === statusSteps.step4">生成推荐结果中，通常需要1小时左右，可多刷新几次，查看推荐结果</span> -->
                        <span class="wait-online-memo"
                              v-if="item.status === statusSteps.step3_4 || item.status === statusSteps.step4">生成推荐结果中，通常需要1小时左右</span>
                        <div class="operate-btn">
                            <el-button @click.stop="viewCode(item.id)" v-if="showViewCode(item)">查看推荐栏代码</el-button>
                            <el-button @click.stop="viewReport(item)" v-if="item.status >statusSteps.step3_4">查看报表
                            </el-button>
                            <el-button @click.stop="setRule(item.id)">配置运营规则</el-button>
                            <el-button @click.stop="goContentManage(item)">内容管理</el-button>
                            <el-button @click.stop="showAddScene(item)">推荐位样式</el-button>
                            <el-dropdown trigger="click" placement='bottom' @command="deleteScene(item)">
                                <el-button class="delete-btn">更多操作
                                    <i class="el-icon-arrow-down el-icon--right"></i>
                                </el-button>
                                <el-dropdown-menu slot="dropdown">
                                    <el-dropdown-item>删除</el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </div>
                    </div>
                    <div class="_paradigm-online-item-scope">
                        <div class="report-item">
                            <div class="uv-icon report-icon">UV</div>
                            <div class="report-div">
                                <div class="report-memo">
                                    <span class="top-memo">昨日总UV</span>
                                    <span class="report-num">{{item.report.uvCount&&item.report.uvCount!=='0'?item.report.uvCount:'--'}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="report-item">
                            <div class="pv-icon report-icon">PV</div>
                            <div class="report-div">
                                <div class="report-memo">
                                    <span class="top-memo">昨日总PV</span>
                                    <span class="report-num">{{item.report.pvCount&&item.report.pvCount!=='0'?item.report.pvCount:'--'}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="report-item">
                            <div class="ctr-icon report-icon">CTR</div>
                            <div class="report-div">
                                <div class="report-memo">
                                    <span class="top-memo">点击率CTR</span>
                                    <span class="report-num">{{item.report.ctrShow&&item.report.ctrShow!=='0.000%'?item.report.ctrShow:'--'}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <el-dialog title="推荐栏自定义位置代码" :visible.sync="showSdk" id="plug-code-dialog-scope" @close='closedCodeDialog'>
                <div class="plug-code-dialog-content">
                    <div class="item-box">
                        <p>
                            推荐栏创建成功，请将以下代码，复制到您网站推荐栏位置处。
                        </p>
                        <el-input :rows="10" type="textarea" ref="jsTextarea" v-model="pluginDiyCode"
                                  readonly=""></el-input>
                        <el-button @click="copy('jsTextarea')">复制</el-button>
                    </div>
                </div>
                <div slot="footer" class="dialog-footer">
                    <el-button type="primary" @click="hide">确定</el-button>
                </div>
            </el-dialog>
            <!-- 运营规则设置 start -->
            <el-dialog title="配置规则" :visible.sync="showRuleDialog" class="operation-dialog"
                       :close-on-click-modal='false'
                       id="operation-edit-scope">
                <div id="operation-dialog" v-if="showRuleDialog">
                    <!-- 修改排序规则 -->
                    <div>
                        <div class="edit-rule-item-title">排序规则</div>
                        <el-card class="rule-type sort-rule sort-rule-info">
                            <div slot="header">
                                <span class="sort-index-column">序号</span>
                                <span class="sort-field-column">特征</span>
                                <span class="sort-field-value-column">特征值</span>
                                <span class="sort-treatment-column">加权系数</span>
                                <span class="sort-opr-column">操作</span>
                                <span class="sort-alert-column">提示</span>
                            </div>
                            <div class="rule-item">
                                <div class="rule-show-item rule-show-noborder-item" v-for="(item,index) in sortRuleList"
                                     :key="index">
                                    <span class="sort-index-column">{{index+1}}</span>
                                    <span class="sort-field-column">
                      <el-select class="rule-sort-field-select" v-model="item.field" placeholder="请选择特征"
                                 @change="changeSortField(item)">
                        <el-option v-for="item in  sortSelectList" :key="item.value" :label="item.name"
                                   :value="item.value"></el-option>
                      </el-select>
                    </span>
                                    <span class="sort-field-value-column" v-if="item.field === 'category_v1'">
                      <el-select class="rule-sort-field-value-select" v-model="item.value" filterable
                                 placeholder="请选择特征值">
                        <el-option v-for="(item,index) in  categoryV1List" :key="index" :label="item"
                                   :value="item"></el-option>
                      </el-select>
                    </span>
                                    <span class="sort-field-value-column" v-else>-</span>
                                    <span class="sort-treatment-column">
                      <el-input-number v-model="item.treatment" :min=-10 :max=10 class="rule-input-num"
                                       @blur="checkSortInputNum(item,index)"
                                       ref="checkSortInputNum"></el-input-number>
                    </span>
                                    <span class="sort-opr-column">
                      <el-button class="delete-button" @click="deleteSortRule(index)">
                        <i class="icon-delete"></i>
                        删除
                      </el-button>
                    </span>
                                    <el-popover placement="right" width="200" trigger="hover"
                                                :content="sortAlertInfoList[item.field]"
                                                popper-class="ruleEditTablePop" v-if="item.field">
                      <span class="sort-alert-column" slot="reference"><img class="sort-alert-grey"
                                                                            src="<?php echo plugins_url('static/img/alert_grey.svg', __FILE__) ?>"
                                                                            slot="reference"/></span>
                                    </el-popover>
                                </div>
                                <div class="rule-show-item rule-show-noborder-item">
                                    <div class="add-extract-feature link-primary add-diversity-rule"
                                         @click="addSortRule">
                                        <i class="el-icon-circle-plus"></i>
                                        添加一条排序规则
                                    </div>
                                </div>
                            </div>
                        </el-card>
                    </div>
                    <!-- 多样性规则,关联推荐时隐藏-->
                    <!-- 修改多样性规则 -->
                    <div v-if="currentSceneInfo && currentSceneInfo.scene && currentSceneInfo.scene.recommendType !== 1">
                        <div class="edit-rule-item-title">多样性规则</div>
                        <el-card class="rule-type diversity-rule diversity-rule-info">
                            <div slot="header">
                                <span class="diversity-index-column">序号</span>
                                <span class="diversity-field-column">特征</span>
                                <span class="diversity-condition-column">条件</span>
                                <span class="diversity-keyword-column">关键词</span>
                                <span class="diversity-opr-column">操作</span>
                                <span class="diversity-limit-column">限制</span>
                            </div>
                            <div class="rule-item" v-for="(rule,groupIndex) in diversityRuleList" :key="groupIndex"
                                 v-if="rule.hidden!==1">
                                <div class="rule-show-item rule-show-noborder-item" v-for="(item,index) in rule.script"
                                     :key="item.id">
                    <span class="diversity-index-column">
                      {{index === 0 ? groupIndex-hiddenRuleExistIndex + 1 : '且'}}
                    </span>
                                    <span class="diversity-field-column">
                      <el-select class="rule-diversity-field-select" v-model="item.field" placeholder="请选择特征"
                                 @change="changeField(item)">
                        <el-option v-for="item in  diversitySelectList" :key="item.value" :label="item.name"
                                   :value="item.value"></el-option>
                      </el-select>
                    </span>
                                    <span class="diversity-condition-column">
                      <el-select class="rule-diversity-condition-select" v-model="item.op" placeholder="请选择条件"
                                 @change='changeOp(item)'>
                        <el-option v-for="item in  getConditionList(item.field)" :key="item.value" :label="item.label"
                                   :value="item.value"></el-option>
                      </el-select>
                    </span>
                                    <span class="diversity-keyword-column" v-if="item.op === '=='">
                      <template v-if="item.field === 'publisher_id'">
                        <el-input v-model="item.value" class='diversity-keyword-input' placeholder="请输入关键字"
                                  :maxlength="20">
                          <i slot="suffix" class="diversity-keyword-label">{{item.value?item.value.length:0}}/20</i>
                        </el-input>
                        <div class='error-msg-sec'>{{getKeyWordErrorAlert(item.value)}}</div>
                      </template>
                      <el-select class="diversity-keyword-select" v-model="item.value" filterable placeholder="请选择关键字"
                                 v-else>
                        <el-option v-for="(item,index) in  categoryV1List" :key="index" :label="item"
                                   :value="item"></el-option>
                      </el-select>
                    </span>
                                    <span class="diversity-keyword-column" v-else>
                      -
                    </span>
                                    <span class="diversity-opr-column">
                      <el-button class="delete-button" @click="removeDiversityC(groupIndex,index)">
                        <i class="icon-delete"></i>
                        删除
                      </el-button>
                    </span>
                                    <span class="diversity-limit-column" v-if="index===0">
                      <span>不超过</span>
                      <el-input-number v-model="rule.eValue" :min=1 :max="currentSceneInfo.scene.itemCount"
                                       class="rule-input-num diversity-limit-input-num"
                                       @blur="checkDiversityInputNum(rule,groupIndex-hiddenRuleExistIndex)"
                                       ref="checkDiversityInputNum"></el-input-number>
                      <span>条</span>
                    </span>
                                </div>
                                <div class="rule-show-item rule-show-noborder-item ">
                                    <div class="rule-opr-flex">
                                        <div class="add-extract-feature link-primary add-content-rule"
                                             @click="addDiversityC(groupIndex)"
                                             v-if="rule.script.length<3">
                                            添加内容筛选条件
                                        </div>
                                    </div>

                                    <el-button class="delete-button delete-group"
                                               @click="deleteDiversityRule(groupIndex)">
                                        <i class="icon-delete"></i>
                                        删除该组规则
                                    </el-button>
                                </div>
                            </div>
                            <div class="rule-show-item rule-show-noborder-item">
                                <div class="add-extract-feature link-primary add-diversity-rule"
                                     @click="addDiversityRule">
                                    <i class="el-icon-circle-plus"></i>
                                    添加一条多样性规则
                                </div>
                            </div>
                        </el-card>
                    </div>

                </div>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="hideRuleDialog">取 消</el-button>
                    <el-button type="primary" @click="saveRuleConfigAction">确 定</el-button>
                </div>
            </el-dialog>
            <!-- 运营规则设置 end -->
        </div>
        <ad-list-page v-else></ad-list-page>
        <?php include 'footer.php' ?>
    </div>
</div>

<script>
    new Vue({
            el: '#paradigm_scene_list',
            data() {
                return {
                    // 显示tab标示，默认为显示推荐位列表
                    showTabIndex: 'SCENE_LIST',
                    showSdk: false,
                    pluginDiyCode: '',
                    sceneList: [],
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
                    /*****运营规则相关变量 start *******************/
                    // 显示运营规则弹窗的标记
                    showRuleDialog: false,
                    // 当前操作的场景
                    currentSceneInfo: {},
                    // 记录当前设置的规则json
                    ruleJson: {},
                    // 排序规则设置数据列表
                    sortRuleList: [],
                    // 排序规则设置的候选
                    sortSelectList: [{
                        value: 'publish_time',
                        name: '发布时间'
                    },
                        {
                            value: 'item_read_cnt',
                            name: '阅读量'
                        },
                        {
                            value: 'cover_url_cnt',
                            name: '封面图数'
                        },
                        {
                            value: 'category_v1',
                            name: '一级分类'
                        },
                        {
                            value: 'url_cnt',
                            name: '详情页图片数'
                        }
                    ],
                    // 一级分类列表
                    categoryV1List: [],
                    sortAlertInfoList: {
                        publish_time: '加权系数为正时，将发布时间距离现在更近的内容，排列靠前；设置为“0”则不对特征加权；加权系数为负时，将发布时间距离现在更远的内容，排列靠前',
                        item_read_cnt: '加权系数为正时，阅读量更高的内容，排列靠前；设置为“0”则不对特征加权；加权系数为负时，阅读量更高的内容，排列靠后',
                        cover_url_cnt: '加权系数为正时，封面图数量更多的内容，排列靠前；设置为“0”则不对特征加权；加权系数为负时，封面图数量更多的内容，排列靠后',
                        category_v1: '加权系数为正时，和选择具体类别项相同的内容，排列靠前；设置为“0”则不对特征加权；加权系数为负时，和选择具体类别项相同的内容，排列靠后',
                        url_cnt: '加权系数为正时，详情页图片数量更多的内容，排列靠前；设置为“0”则不对特征加权；加权系数为负时，详情页图片数量更多的内容，排列靠后'
                    },
                    //多样性规则设置数据
                    diversityRuleList: [],
                    diversitySelectList: [{
                        value: 'publisher_id',
                        name: '发布者id'
                    },
                        {
                            value: 'category_v1',
                            name: '一级分类'
                        },
                        {
                            value: 'category_id',
                            name: '全部分类'
                        }
                    ],
                    hiddenRuleExistIndex: 0

                    /*****运营规则相关变量 end ******************/
                };
            },
            mounted() {
                this.initSceneList();
                const showID = window._4paradigm_plug_util.getQueryVariable('sceneId');
                if (showID) {
                    this.viewCode(showID)
                }
            },
            methods: {
                // 切换Tab显示的列表
                swichListTab(index) {
                    this.showTabIndex = index;
                },
                logoClick() {
                    window.location.href = ''
                },
                async initSceneList() {
                    let param = {
                        source: '7',
                        plugSiteId: document.getElementById('paradigm_sitId') && document.getElementById('paradigm_sitId').getAttribute(
                            'value') || ''
                    }
                    let {
                        data
                    } = await window._4paradigm_plug_API.getSceneList(param);
                    if (data) {
                        this.sceneList = data
                    }
                },
                showViewCode(item) {
                    const configStr = item.plugConfig
                    if (configStr) {
                        const config = JSON.parse(configStr)
                        return config.recomLocation === 'DIY'
                    }
                    return false
                },
                // 获取当前的状态
                getCurrentStatus(item) {
                    let status = item.status
                    if (
                        status === this.statusSteps.step3_4 ||
                        status === this.statusSteps.step4
                    ) {
                        return '上线中'
                    } else if (status === this.statusSteps.step5) {
                        return '已上线'
                    } else {
                        return '配置中'
                    }
                },
                // 获取当前状态的显示样式
                getTagStyle(item) {
                    let status = item.status
                    // 待上线
                    if (
                        status === this.statusSteps.step3_4 ||
                        status === this.statusSteps.step4
                    ) {
                        return 'warning'
                        // 已上线
                    } else if (status === this.statusSteps.step5) {
                        return 'success'
                    } else {
                        return 'danger'
                    }
                },
                showAddScene(item) {
                    let phpUrl = window.location.origin + window.location.pathname;
                    window.location.href = phpUrl +
                        '?page=rec_xianjian_rec_options1&sceneNum=' + this.sceneList.length + '&sceneId=' + (
                            item && item.id || '')
                },
                // 删除场景
                async deleteScene(item) {
                    const channel = document.getElementById('paradigm_plugChannel') && document.getElementById(
                        'paradigm_plugChannel').getAttribute(
                        'value') || '';
                    const lastSceneBeeFlg = channel === 'plugin_official_beepress' && this.sceneList.length === 1;
                    this.$confirm(lastSceneBeeFlg ? '删除此推荐位，将导致小蜜蜂采集功能不可用(蜜蜂采集付费用户不受影响)，请谨慎操作，确认继续?' : '确认删除场景?',
                        lastSceneBeeFlg ? '警告' :
                            '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'error'
                        })
                        .then(async () => {
                            let {
                                code,
                                info
                            } = await window._4paradigm_plug_API.removeScene({
                                sceneId: item.id
                            })
                            if (code === 200) {
                                this.$message({
                                    type: 'success',
                                    message: '删除成功!',
                                    duration: 1500
                                })
                                this.initSceneList()
                                paradigmPostPlugConfigToWordPress({
                                    type: 'delete',
                                    sceneId: item.id
                                })
                            } else {
                                this.$message.error(info)
                            }
                        })
                        .catch(() => {
                        })
                },
                // 查看报表
                viewReport(item) {
                    let phpUrl = window.location.origin + window.location.pathname;
                    window.location.href = phpUrl +
                        '?page=rec_xianjian_rec_options2&sceneId=' + (
                            item && item.id || 0)

                },
                // 内容管理
                goContentManage(item) {
                    let phpUrl = window.location.origin + window.location.pathname;
                    window.location.href = phpUrl +
                        '?page=rec_xianjian_rec_options3&sceneId=' + (
                            item && item.id || 0) + '&itemSetId=' + (
                            item && item.itemSetId || 0)
                },
                /******************** 查看自定义代码 start**************************** */
                viewCode(itemId) {
                    this.showSdk = true
                    this.renderCodeJs(itemId)

                },
                hide() {
                    this.showSdk = false
                },
                closedCodeDialog() {
                    const showID = window._4paradigm_plug_util.getQueryVariable('sceneId');
                    if (showID) {
                        let phpUrl = window.location.origin + window.location.pathname;
                        window.location.href = phpUrl +
                            '?page=rec_xianjian_rec_options'
                    }
                },
                // 获取自定义位置代码
                async renderCodeJs(itemId) {
                    let {
                        data
                    } = await window._4paradigm_plug_API.getRenderCodeJs({
                        sceneId: itemId,
                        plug: true
                    })
                    if (data && data.renderJs) {
                        this.pluginDiyCode = data.renderJs
                    }
                },
                // 复制
                copy(type) {
                    let textarea = this.$refs[type].$refs.textarea
                    textarea.select()
                    document.execCommand('Copy')
                    this.$message({
                        duration: 2000,
                        message: '复制成功',
                        type: 'success'
                    })
                },
                /******************** 查看自定义代码 end**************************** */
                /******************** 配置运营规则 start**************************** */

                // 设置运营规则
                async setRule(itemId) {
                    this.categoryV1List = [];
                    this.sortRuleList = []
                    this.showRuleDialog = true;
                    this.currentSceneInfo = {}
                    let {
                        data
                    } = await window._4paradigm_plug_API.getSceneDetail({
                        sceneId: itemId
                    })
                    this.currentSceneInfo = data;
                    // 记录操作的运营规则对象(用于判断是否调用保存)
                    if (!this.currentSceneInfo.sceneRule || !this.currentSceneInfo.sceneRule.ruleJson) {
                        this.currentSceneInfo.sceneRule = {
                            ruleJson: '{"rules":{"scategory":[{"script":[]}],"lrules":[],"csaas":[]},"type":' + this.currentSceneInfo
                                .scene.recommendType + '}'
                        }
                    }
                    this.ruleJson = this.currentSceneInfo.sceneRule.ruleJson
                    this.getCategoryV1List()
                    this.initSortRuleList()
                    this.initDiversityRuleList()
                },

                // 关闭运营规则弹窗
                hideRuleDialog() {
                    this.showRuleDialog = false;
                },
                // 保存运营规则
                async comfirmRule() {
                    let scene = this.currentSceneInfo.scene
                    if (this.ruleJson === this.currentSceneInfo.sceneRule.ruleJson) {
                        this.hideRuleDialog()
                        return
                    }
                    let {
                        code,
                        info
                    } = await window._4paradigm_plug_API.saveConfigRule({
                        sceneId: scene.id,
                        recommendType: scene.recommendType,
                        ruleJson: this.currentSceneInfo.sceneRule.ruleJson
                    })
                    if (code === 200) {
                        this.$message({
                            duration: 2000,
                            message: '您的运营规则配置成功，规则将会在5分钟后生效',
                            type: 'success'
                        })
                        this.hideRuleDialog()
                    } else {
                        this.$message({
                            duration: 2000,
                            message: info,
                            type: 'error'
                        })
                    }
                },

                // 点击保存运营设置
                saveRuleConfigAction() {
                    // 保存排序规则
                    if (!this.saveSortRule()) {
                        return
                    }
                    // 保存多样性规则
                    if (this.currentSceneInfo.scene.recommendType !== 1 && !this.saveDiversityRule()) {
                        return
                    }
                    this.comfirmRule()
                },
                /**********************排序规则相关 start***/
                // 新增排序规则
                addSortRule() {
                    this.sortRuleList.push({
                        field: '',
                        value: '',
                        treatment: 1
                    })
                },
                // 删除某一条排序规则
                deleteSortRule(index) {
                    let cb = () => {
                        this.sortRuleList.splice(index, 1)
                    }
                    this.confirmSortDelete(cb)
                },
                // 确认删除提示框
                confirmSortDelete(cb) {
                    this.$confirm('确认删除?', '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'error'
                    })
                        .then(() => {
                            cb.apply(this)
                            this.$message({
                                type: 'success',
                                message: '删除成功!',
                                duration: 2000
                            })
                        })
                        .catch(() => {
                        })
                },
                changeSortField(item) {
                    item.value = ''
                },
                checkSortInputNum(item, index) {
                    if (item.treatment === '' || typeof (item.treatment) === 'undefined') {
                        item.treatment = 1
                        this.$refs.checkSortInputNum[index].setCurrentValue(item.treatment)
                    }
                },
                // 初始化排序规则设置的数据
                initSortRuleList() {
                    let sceneRule =
                        this.currentSceneInfo && this.currentSceneInfo.sceneRule && this.currentSceneInfo.sceneRule.ruleJson ?
                            JSON.parse(this.currentSceneInfo.sceneRule.ruleJson) : {
                                rules: {}
                            }
                    if (sceneRule.rules.csaas) {
                        this.sortRuleList = sceneRule.rules.csaas
                    } else {
                        this.sortRuleList = []
                    }
                },
                // 获取一级分类列表
                async getCategoryV1List() {
                    let {
                        data
                    } = await window._4paradigm_plug_API.getMateCategoryV1({
                        itemSetId: this.currentSceneInfo.itemSet.id
                    })
                    this.categoryV1List = data
                },
                // 保存排序规则
                saveSortRule() {
                    let sceneRule =
                        this.currentSceneInfo && this.currentSceneInfo.sceneRule && this.currentSceneInfo.sceneRule.ruleJson ?
                            JSON.parse(this.currentSceneInfo.sceneRule.ruleJson) : {
                                rules: {}
                            }
                    let fields = []
                    let categoryV1Value = []
                    for (let item of this.sortRuleList) {
                        if (!item.field) {
                            continue
                        }
                        if (fields.includes(item.field) && item.field !== 'category_v1') {
                            this.$message.error('特征规则重复，请您修改调整排序规则设置')
                            return false
                        }
                        if (item.field === 'category_v1') {
                            if (!item.value) {
                                this.$message.error(
                                    '请选择一级分类特征值'
                                )
                                return false
                            }
                            if (categoryV1Value.includes(item.value)) {
                                this.$message.error(
                                    '一级分类特征值设置重复，请您检查并修改分类项选择'
                                )
                                return false
                            }
                            categoryV1Value.push(item.value)
                        } else {
                            item.value = '::sort.desc'
                        }
                        fields.push(item.field)
                    }
                    sceneRule.rules.csaas = this.sortRuleList.filter(item => item.field)
                    this.currentSceneInfo.sceneRule.ruleJson = JSON.stringify(sceneRule)
                    return true
                },
                /************************排序规则相关 end****/
                /************************多样性规则相关 start****/
                // 保存规则
                saveDiversityRule() {
                    const reg = /^[a-zA-Z0-9\u4e00-\u9fa5]+$/
                    let sceneRule =
                        this.currentSceneInfo && this.currentSceneInfo.sceneRule && this.currentSceneInfo.sceneRule.ruleJson ?
                            JSON.parse(this.currentSceneInfo.sceneRule.ruleJson) : {
                                rules: {}
                            }
                    let validRuleList = []
                    let ruleFieldGroup = {}
                    for (let i = 0; i < this.diversityRuleList.length; i++) {
                        const item = this.diversityRuleList[i]
                        let scriptList = []
                        let filedMap = new Map()
                        for (let rule of item.script) {
                            if (rule.field) {
                                if (
                                    rule.op === '==' &&
                                    (rule.field === 'publisher_id' || rule.field === 'category_v1')
                                ) {
                                    if (rule.value) {
                                        if (!reg.test(rule.value)) {
                                            this.$message.error('关键词只支持中文、字母和数字组合')
                                            return false
                                        }
                                    } else {
                                        // 等于条件，需要填入关键字
                                        this.$message.error(
                                            '您设置的规则中，存在关键词未填写，请检查并输入关键词'
                                        )
                                        return false
                                    }
                                }
                                if (filedMap.has(rule.field)) {
                                    this.$message.error(
                                        '同组多样性规则中，存在特征选择的冲突，相同特征设置请在不同组规则中设置'
                                    )
                                    return false
                                }
                                if (!ruleFieldGroup[rule.field]) {
                                    ruleFieldGroup[rule.field] = []
                                }
                                ruleFieldGroup[rule.field].push(rule)
                                filedMap.set(rule.field, rule.field)
                                scriptList.push(rule)
                            }
                        }
                        if (scriptList.length > 0) {
                            item.script = scriptList
                            item.id = this.scenceId + '_' + i
                            validRuleList.push(item)
                        }
                    }
                    // 校验不同分组中的规则，是否存在冲突的
                    for (let field in ruleFieldGroup) {
                        const diversityRuleList = ruleFieldGroup[field]
                        let likeRuleList = []
                        let equleRuleMap = new Map()
                        for (let rule of diversityRuleList) {
                            if (rule.op === '==') {
                                if (equleRuleMap.has(rule.value.trim())) {
                                    this.$message.error(
                                        '不同组多样性规则中，存在规则冲突，请您检查并修改配置'
                                    )
                                    return false
                                }
                                equleRuleMap.set(rule.value.trim())
                            } else {
                                // 包含多个，特征值相同且条件为任意相同
                                if (likeRuleList.length > 0) {
                                    // 不同同组中存在冲突
                                    this.$message.error(
                                        '不同组多样性规则中，存在规则冲突，请您检查并修改配置'
                                    )
                                    return false
                                }
                                likeRuleList.push(rule)
                            }
                        }
                    }
                    this.diversityRuleList = validRuleList
                    sceneRule.rules.lrules = this.diversityRuleList
                    this.currentSceneInfo.sceneRule.ruleJson = JSON.stringify(sceneRule)
                    return true
                },
                initDiversityRuleList() {
                    let sceneRule =
                        this.currentSceneInfo && this.currentSceneInfo.sceneRule && this.currentSceneInfo.sceneRule.ruleJson ?
                            JSON.parse(this.currentSceneInfo.sceneRule.ruleJson) : {
                                rules: {}
                            }
                    if (sceneRule.rules.lrules) {
                        this.diversityRuleList = sceneRule.rules.lrules
                    } else {
                        this.diversityRuleList = []
                    }
                    this.hiddenRuleExistIndex = 0
                    if (this.diversityRuleList && this.diversityRuleList.length) {
                        for (let rule of this.diversityRuleList) {
                            if (rule.hidden && rule.hidden === 1) {
                                this.hiddenRuleExistIndex += 1
                            }
                        }
                    }
                },
                // 新增多样性规则
                addDiversityRule() {
                    this.diversityRuleList.push({
                        usage: 'DIVERSITY',
                        script: [],
                        eValue: 1
                    })
                },
                // 删除某一条多样性规则
                deleteDiversityRule(index) {
                    let cb = () => {
                        this.diversityRuleList.splice(index, 1)
                    }
                    this.confirmDiversityDelete(cb)
                },
                // 添加多样性规则内容
                addDiversityC(groupIndex) {
                    const cRuleList = this.diversityRuleList[groupIndex].script
                    cRuleList.push({
                        field: '',
                        op: '',
                        value: '',
                        type: 1
                    })
                },
                // 删除多样性规则内容
                removeDiversityC(groupIndex, index) {
                    const cRuleList = this.diversityRuleList[groupIndex].script
                    let cb = () => {
                        cRuleList.splice(index, 1)
                    }
                    this.confirmDiversityDelete(cb)
                },
                // 确认删除提示框
                confirmDiversityDelete(cb) {
                    this.$confirm('确认删除?', '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'error'
                    })
                        .then(() => {
                            cb.apply(this)
                            this.$message({
                                type: 'success',
                                message: '删除成功!',
                                duration: 2000
                            })
                        })
                        .catch(() => {
                        })
                },
                getConditionList(field) {
                    const likeObj = {
                        value: 'any',
                        label: '任意相同'
                    }
                    const equelObj = {
                        value: '==',
                        label: '='
                    }
                    if (field === 'publisher_id') {
                        return [likeObj, equelObj]
                    } else if (field === 'category_v1') {
                        return [likeObj, equelObj]
                    } else {
                        return [likeObj]
                    }
                },
                // 切换特征
                changeField(item) {
                    item.op = ''
                    item.value = ''
                },
                // 切换条件
                changeOp(item) {
                    item.value = ''
                },
                getKeyWordErrorAlert(value) {
                    const reg = /^[a-zA-Z0-9\u4e00-\u9fa5]+$/
                    if (value) {
                        if (!reg.test(value)) {
                            return '支持中文、字母和数字组合'
                        } else {
                            return ''
                        }
                    } else {
                        return ''
                    }
                },
                checkDiversityInputNum(item, index) {
                    if (!item.eValue) {
                        item.eValue = 1
                        this.$refs.checkDiversityInputNum[index].setCurrentValue(1)
                    }
                }
                /************************多样性规则相关 end****/
                /******************** 配置运营规则 end**************************** */
            }
        },
    );
</script>
</body>
</html>