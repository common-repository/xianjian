<html lang="zh">

<head>
  <meta charset="UTF-8">
  <title>先荐--场景配置</title>
  <?php include 'config_common.php'?>
  <?php include 'nav.php'?>
</head>

</head>

<body class="paradigm_body">
  <div id='paradigm_config_page' class='paradigm-app' v-cloak>
    <div class='_paradigm-content-plugIn'>
      <paradigm-nav @logo-click='logoClick'></paradigm-nav>
      <div class="_paradigm-breadcrumb-list">
        <ul>
          <li class="_paradigm-breadcrumb-item" @click='logoClick("")'>
            &lt;&lt;推荐栏列表
        </ul>
      </div>
      <div class="plugin-baseConfig-scope">
        <div class="plugin-baseConfig-scope-content" ref="plugin-baseConfig-scope-content">
          <span class="plugin-baseConfig-memo">完成推荐栏“基本设置”和“样式设置”即可上线推荐栏，提升PV仅需一步！</span>
          <div class="plugin-fe-config-base-scope">
            <div class="plugin-config-title">基本设置</div>
            <div class="plugin-config-item">
              <span class="plugin-config-item-label">推荐名称</span>
              <el-input class="plugin-config-name-input" placeholder="用于插件后台区分不同的推荐栏" v-model="baseConfig.sceneName"
                :maxlength="25" @focus="nameErr.info = ''"></el-input>
              <div class='plugin-config-name-err'>{{nameErr.info}}</div>
            </div>
            <div class="plugin-config-item">
              <span class="plugin-config-item-label">推荐栏显示名称</span>
              <el-input class="plugin-config-name-input" placeholder="" v-model="baseConfig.recomTitle" :maxlength="25"
                @focus="nameErr.info = ''"></el-input>
              <div class='plugin-config-name-err'>{{nameErr.info}}</div>
            </div>
            <div class="plugin-config-item">
              <span class="plugin-config-item-label">推荐栏位置</span>
              <el-radio-group v-model="baseConfig.recomLocation" @change="changeRecomLoaction" :disabled="disableChangeLocation">
                <el-radio v-for="(item,index) in recomLocationList" :key="index" :label="item.key">{{item.name}}</el-radio>
              </el-radio-group>
            </div>
            <template v-if="baseConfig.recomLocation==='DIY' && baseConfig.id !==0">
              <div class="plugin-config-item">
                <span class="plugin-config-item-label">自定义代码</span>
                <div class="plugin-diy-item">
                  <div class="plugin-diy-memo">为获取网站内容，请您将代码粘贴到推荐栏处</div>
                  <el-button class="plugin-diy-copy" @click="copyCode">复制代码</el-button>
                </div>
              </div>
              <el-input class="plugin-diy-code" type="textarea" readonly ref="pluginDiyCode" v-model="pluginDiyCode"></el-input>

            </template>
            <div class="plugin-config-item" v-if="baseConfig.recomLocation!=='DIY'">
              <span class="plugin-config-item-label">页面位置</span>
              <el-radio-group v-model="baseConfig.pageLocation" @change="changePageLocation">
                <el-radio v-for="(item,index) in pageLocationList" :key="index" :label="item.key">{{item.name}}</el-radio>
              </el-radio-group>
            </div>
            <div class="plugin-config-item">
              <span class="plugin-config-item-label">推荐类型</span>
              <el-radio-group v-model="baseConfig.recommendType" @change="changeRecommendType">
                <el-radio :label="3">个性化推荐
                  <el-tooltip class="item" effect="dark" content='根据用户阅读喜好，给用户推荐个性化的文章' placement="bottom">
                    <span class="el-icon-question set-msg" />
                  </el-tooltip>
                </el-radio>
                <el-radio :label="1" v-if="baseConfig.recomLocation!=='HOME'">关联推荐
                  <el-tooltip class="item" effect="dark" content='给用户推荐当前页面文章相关的文章' placement="bottom">
                    <span class="el-icon-question set-msg" />
                  </el-tooltip>
                </el-radio>
                <el-radio :label="2">热门推荐
                  <el-tooltip class="item" effect="dark" content='给用户推荐全站最热门的文章' placement="bottom">
                    <span class="el-icon-question set-msg" />
                  </el-tooltip>
                </el-radio>
              </el-radio-group>
            </div>
            <div class="plugin-config-item" v-if="openShowType">
              <span class="plugin-config-item-label">布局类型</span>
              <el-radio-group v-model="baseConfig.showType" @change="changeShowType" :disabled='baseConfig.id !==0 && baseConfig.id !=="0"'>
                <el-radio :label="ShowTypeEnum.FEED">信息流（无限下拉）
                </el-radio>
                <el-radio :label="ShowTypeEnum.FIXED">固定长度
                </el-radio>
              </el-radio-group>
            </div>
            <!-- 选择正文页/评论栏下方/布局类型下方增加温馨提示 -->
            <div class="plugin-config-feed-memo" v-if="baseConfig.pageLocation === 'C_B'">
              如信息流未在前端页面正常显示，可能是兼容问题，请选择自定义位置，手动粘贴代码到推荐位处即可
            </div>
          </div>
          <div class="plugin-baseConfig-divider"></div>
          <div class="plugin-fe-config-typeSet-scope">
            <div class="plugin-config-title">推荐栏样式</div>
            <div class="plugin-config-typeset-container">
              <div v-for="(item,index) in typeSetDemoList" :key="index" class="plugin-config-typeset-item" :class="baseConfig.typeSet===item.key?'active':''"
                @click="setTypeSet(item.key)">
                <img class="plugin-config-typeset-img" :src='imgPath + item.img' />
                <span class="plugin-config-typeset-label">{{item.label}}</span>
                <div class="plugin-config-typeset-check">
                  <i class="el-icon-check"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="plugin-baseConfig-divider"></div>
        <div class="plugin-baseConfig-footer">
          <el-button type='primary' @click="saveConfig(0)" :disabled="!baseConfig.sceneName">保存并上线</el-button>
          <el-button @click="goConfigDetail" :disabled="!baseConfig.sceneName">设置更多样式</el-button>
        </div>
      </div>
      <?php include 'footer.php'?>
    </div>
  </div>

  </template>

  <script>
    const paradigm_plugChannel = document.getElementById('paradigm_plugChannel') && document.getElementById(
      'paradigm_plugChannel').getAttribute(
      'value') || '';
    const sideBarPagePositionList = [{
      key: 'S',
      name: '侧边栏'
    }]
    const pagePagePositionList = [{
        key: 'P_T',
        name: '页面顶部'
      },
      {
        key: 'P_B',
        name: '页面底部'
      }
    ]
    // 基本设置的样式设置的显示数据列标（固定位置）
    const typeSetFixedDemoList = [{
        key: window._4paradigm_plug_util.TypeSetEnum.ONLY_TITLE,
        label: '纯标题',
        img: 'ONLY_TITLE.png'
      },
      {
        key: window._4paradigm_plug_util.TypeSetEnum.L_TITLE_R_TIME,
        label: '左标题右时间',
        img: 'L_TITLE_R_TIME.png'
      },

      {
        key: window._4paradigm_plug_util.TypeSetEnum.TITLE_ABSTRACT,
        label: '标题摘要',
        img: 'TITLE_ABSTRACT.png'
      },
      {
        key: window._4paradigm_plug_util.TypeSetEnum.L_IMG_R_TXT,
        label: '左图右文',
        img: 'L_IMG_R_TXT.png'
      },
      {
        key: window._4paradigm_plug_util.TypeSetEnum.UP_IMG_DOWN_TXT,
        label: '上图下文',
        img: 'UP_IMG_DOWN_TXT.png'
      },
      {
        key: window._4paradigm_plug_util.TypeSetEnum.UP_TXT_DOWN_IMG,
        label: '上文下图',
        img: 'UP_TXT_DOWN_IMG.png'
      }
    ]

    // 基本设置的样式设置的显示数据列标（feed流）
    const typeSetFeedDemoList = [{
        key: window._4paradigm_plug_util.TypeSetEnum.AUTO,
        label: '自动排版',
        img: 'AUTO.png'
      },
      {
        key: window._4paradigm_plug_util.TypeSetEnum.ONLY_TITLE,
        label: '纯标题',
        img: 'ONLY_TITLE_F.png'
      },
      {
        key: window._4paradigm_plug_util.TypeSetEnum.L_TITLE_R_TIME,
        label: '左标题右时间',
        img: 'L_TITLE_R_TIME_F.png'
      },

      {
        key: window._4paradigm_plug_util.TypeSetEnum.TITLE_ABSTRACT,
        label: '标题摘要',
        img: 'TITLE_ABSTRACT.png'
      },
      {
        key: window._4paradigm_plug_util.TypeSetEnum.L_IMG_R_TXT,
        label: '左图右文',
        img: 'L_IMG_R_TXT.png'
      },
      {
        key: window._4paradigm_plug_util.TypeSetEnum.UP_IMG_DOWN_TXT,
        label: '上图下文',
        img: 'UP_IMG_DOWN_TXT.png'
      },
      {
        key: window._4paradigm_plug_util.TypeSetEnum.UP_TXT_DOWN_IMG,
        label: '上文下图',
        img: 'UP_TXT_DOWN_IMG.png'
      },
      {
        key: window._4paradigm_plug_util.TypeSetEnum.GROUP_PHOTO,
        label: '组图',
        img: 'GROUP_PHOTO.png'
      }
    ]
    new Vue({
      el: '#paradigm_config_page',
      data() {
        return {
          sceneNameReg: /^[0-9a-zA-Z\u4e00-\u9fa5]{1,25}$/,
          nameErr: {
            info: ''
          },
          baseConfig: {
            id: window._4paradigm_plug_util.getQueryVariable('sceneId') || 0,
            sceneName: paradigm_plugChannel ? ('新建推荐栏' + window._4paradigm_plug_util.getQueryVariable('sceneNum') ||
              0) : '',
            // 默认为个性化推荐
            recommendType: 3,
            // 推荐栏位置
            recomLocation: 'TXT',
            // 页面位置
            pageLocation: 'A_B',
            // 选中的布局类型（默认为固定位置）
            showType: window._4paradigm_plug_util.ShowTypeEnum.FIXED,
            // 选中的排版方式
            typeSet: window._4paradigm_plug_util.TypeSetEnum.ONLY_TITLE,
            // 推荐栏显示title
            recomTitle: '猜你喜欢'
          },
          // 详细配置
          feConfigData: {},
          ShowTypeEnum: window._4paradigm_plug_util.ShowTypeEnum,
          TypeSetEnum: window._4paradigm_plug_util.TypeSetEnum,
          pluginDiyCode: '',
          recomLocationList: [{
              key: 'TXT',
              name: '正文页'
            },
            {
              key: 'HOME',
              name: '首页'
            },
            {
              key: 'DIY',
              name: '自定义页面'
            }
          ],
          // 当选择有图片的布局，判断物料库中是否存在足够多的图片
          judgeContentImgFlg: true,
          imgPath: '<?php  echo plugins_url('static/img/', __FILE__) ?>',
          // 自定义位置的修改允许修改推荐位置
          disableChangeLocation: false
        }
      },
      mounted() {
        if (_paradigm_is_detail_config) { // 点击设置详细设置的回调
          _paradigm_is_detail_config = false
          this.baseConfig.id = _paradigm_is_detail_config_sceneId || 0
          this.showDetailConfigConfirm()
        }
        if (this.baseConfig.id) {
          this.getDetail();
          this.renderCodeJs();
        }

        this.feConfigData = window._4paradigm_plug_util.getDefualtPlugInConfigbyTypeSet(
          paradigm_plugChannel,
          this.baseConfig.showType,
          this.baseConfig.typeSet
        )

      },
      computed: {
        // 页面位置
        pageLocationList() {
          let list = []
          if (this.baseConfig.recomLocation === 'HOME') {
            // 版本号为4.7及以上的可以支持首页底部栏添加推荐场景
            let hightVersion = false
            let wpVersion = document.getElementById('paradigm_wpVersion') && document.getElementById(
              'paradigm_wpVersion').getAttribute(
              'value') || ''
            if (wpVersion) {
              // 版本号
              const versionList = wpVersion.split('.')
              if (versionList.length > 1) {
                if (
                  Number(versionList[0]) > 4 ||
                  (Number(versionList[0]) === 4 && Number(versionList[1] >= 7))
                ) {
                  hightVersion = true
                }
              }
            }
            if (hightVersion) {
              list = [{
                  key: 'A_L_B',
                  name: '文章列表下方'
                },
                ...sideBarPagePositionList
              ]
            } else {
              list = [...sideBarPagePositionList]
            }
          } else if (this.baseConfig.recomLocation === 'TXT') {
            list = [{
                key: 'A_B',
                name: '文章下方'
              },
              {
                key: 'C_T',
                name: '评论栏上方'
              },
              {
                key: 'C_B',
                name: '评论栏下方'
              },
              ...sideBarPagePositionList
            ]
          }
          return list
        },
        typeSetDemoList() {
          if (this.baseConfig.showType === ShowTypeEnum.FEED) {
            return typeSetFeedDemoList
          } else {
            return typeSetFixedDemoList
          }
        },
        // 展现布局类型,正文页/评论栏下方或自定义位置可选择feed流
        openShowType() {
          return (
            this.baseConfig.pageLocation === 'C_B' ||
            this.baseConfig.recomLocation === 'DIY'
          )
        }
      },
      methods: {
        logoClick(showDiyCodeId) {
          let phpUrl = window.location.origin + window.location.pathname;
          window.location.href = phpUrl +
            '?page=rec_xianjian_rec_options' + (showDiyCodeId ? '&sceneId=' + showDiyCodeId : "")
        },
        // 获取场景详情，feConfigFlg，只获取详细配置
        async getDetail() {
          // 分别获取场景详情和渲染配置
          let {
            data
          } = await window._4paradigm_plug_API.getSceneDetail({
            sceneId: this.baseConfig.id
          })
          // feConfigData需要从getFeRender上获取
          let feConfigData = {}
          if (data) {
            const renderId = data.renderId
            const sceneModel = data.scene
            if (sceneModel) {
              this.baseConfig.sceneName = sceneModel.name
              this.baseConfig.recommendType = sceneModel.recommendType
              const configStr = sceneModel.plugConfig
              if (configStr) {
                const config = JSON.parse(configStr)
                this.baseConfig.recomLocation = config.recomLocation
                if (this.baseConfig.recomLocation === 'DIY') {
                  this.disableChangeLocation = true
                }
                this.baseConfig.pageLocation = config.pageLocation
              }
            }
            if (renderId) {
              const renderCfgResponse = await window._4paradigm_plug_API.getFeRender({
                renderId
              })
              const renderJson = renderCfgResponse.data.renderJson
              try {
                feConfigData = JSON.parse(renderJson)
              } catch (e) {
                console.error(e)
              }
            }
            if (feConfigData) {
              // 这里深度赋值会导致helperPostions出现多值很悲剧
              this.baseConfig.typeSet = feConfigData.box.typeSet
              this.baseConfig.showType = feConfigData.box.showType
              this.baseConfig.recomTitle = feConfigData.box.title
              this.feConfigData.articleHelper.helperPositions = []
              window._4paradigm_plug_util.deepAssign(this.feConfigData, feConfigData) // 深度赋值
            }
          }
        },
        checkName() {
          if (!this.sceneNameReg.test(this.baseConfig.sceneName)) {
            this.nameErr.info = '只能输入1-25位字母、数字、中文'
            const baseSet = this.$refs['plugin-baseConfig-scope-content']
            if (baseSet) {
              baseSet.scrollTop = 0
            }
            return false
          }
          return true
        },
        getFeConfigData() {
          if (!this.checkName()) {
            return false
          }
          // 已登录
          // 获取渲染默认配置
          if (
            this.feConfigData &&
            this.feConfigData.box &&
            this.feConfigData.box.showType === this.baseConfig.showType &&
            this.feConfigData.box.typeSet === this.baseConfig.typeSet
          ) {
            // 样式没有修改
          } else {

            this.feConfigData = window._4paradigm_plug_util.getDefualtPlugInConfigbyTypeSet(
              paradigm_plugChannel,
              this.baseConfig.showType,
              this.baseConfig.typeSet
            )
            this.feConfigData.box.title = this.baseConfig.recomTitle
            this.feConfigData.articleHelper.helperPositions = []
          }
          return true
        },
        // 详细设置
        goConfigDetail() {
          this.saveConfig(1)
        },
        // 详细设置的确认
        showDetailConfigConfirm() {
          const vm = this;
          this.$confirm('为了保存基本设置信息，已自动为您创建场景。完成更多样式设置后，根据您的情况点击下面按钮（如点击“立即设置”未跳转，请在浏览器被拦截网页中打开并查看）。', {
            confirmButtonText: '立即设置',
            cancelButtonText: '设置完成',
            type: 'warning',
            closeOnClickModal: false,
            distinguishCancelAndClose: true,
            beforeClose: (action, instance, done) => {
              if (action === 'confirm') { // 选择立即设置
                var baseUrl =
                  '<?php echo XIANJIAN_HOST?>' ||
                  'https://nbrecsys.4paradigm.com'
                var siteId = '<?php echo XIANJIAN_ID?>' || ''
                const paradigm_detail_url = baseUrl +
                  '/#/plugInBk/feDetailconfig?id=' + this.baseConfig.id +
                  '&type=wordpress&siteId=' + siteId + '&isOfficial=true'
                window.open(paradigm_detail_url, '_blank')
              } else if (action === 'cancel') { // 选择设置完成
                vm.logoClick()
              } else {
                done()
              }
            }
          }).then(() => {}).catch(() => {});
        },
        // 保存并上线
        async saveConfig(showDetailConfig) {
          if (this.getFeConfigData()) {
            await this.plugSaveScene(showDetailConfig)
          }
        },
        // 保存场景
        async plugSaveScene(showDetailConfig) {
          // 终端类型：1-PC,2-ANDRIOID,3-iOS,4-小程序,5-WAP,6-DISCUZ,7-WORDPRESS
          const terminalType = 7;
          // 保存前设置是否是插件侧边栏
          this.feConfigData.plugInSider = this.baseConfig.pageLocation === 'S'
          const parentDomain = window._4paradigm_plug_util.getParentDomain()
          this.feConfigData.box.title = this.baseConfig.recomTitle
          let para = {
            id: this.baseConfig.id,
            name: this.baseConfig.sceneName,
            recommendType: this.baseConfig.recommendType,
            terminalType: terminalType,
            plugConfig: JSON.stringify(this.baseConfig),
            renderJson: JSON.stringify(this.feConfigData),
            plugSiteId: document.getElementById('paradigm_sitId') && document.getElementById('paradigm_sitId').getAttribute(
              'value') || '',
            domain: parentDomain
          }
          let {
            data,
            code,
            info
          } = await window._4paradigm_plug_API.plugSaveScene(para)
          if (code === 200) {
            if (!this.baseConfig.recomLocation === 'DIY') {
              this.$message({
                type: 'success',
                message: '保存推荐位成功!',
                duration: 1500
              })
            }
            const vm = this
            paradigmPostPlugConfigToWordPress({
              type: 'modify',
              sceneId: data.sceneId,
              itemSetId: data.itemSetId,
              acessToken: data.acessToken,
              clientToken: data.clientToken,
              recomLocation: this.baseConfig.recomLocation,
              pageLocation: this.baseConfig.pageLocation,
              recomTitle: this.feConfigData &&
                this.feConfigData.box &&
                this.feConfigData.box.title ?
                this.feConfigData.box.title : '',
              showCodeDialog: this.baseConfig.recomLocation === 'DIY' && !this.baseConfig.id,
              showDetailConfig: showDetailConfig
            })
          } else {
            // 失败取消设置是否是插件侧边栏
            this.feConfigData.plugInSider = false
            this.$message.error(info)
          }
        },
        changeRecomLoaction() {
          if (this.pageLocationList.length > 0) {
            this.baseConfig.pageLocation = this.pageLocationList[0].key
          } else {
            this.baseConfig.pageLocation = ''
          }
          this.baseConfig.recommendType = 3
          if (this.baseConfig.id === 0 && this.baseConfig.recomLocation === 'DIY') {
            this.$alert(
              '在创建推荐栏成功后，生成自定义位置推荐栏代码，请将代码放到您网站的推荐栏处',
              '提醒', {
                confirmButtonText: '知道了'
              }
            )
          }
          if (this.baseConfig.recomLocation === 'DIY') {
            this.baseConfig.showType = ShowTypeEnum.FEED
            this.changeShowType()
          } else {
            this.changePageLocation()
          }
        },
        changeRecommendType() {
          if (
            this.baseConfig.recommendType === 1 &&
            this.baseConfig.recomLocation === 'DIY'
          ) {
            this.$alert(
              '选择自定义位置相关推荐代码必须粘贴到文章详情页，否则可能无法显示结果',
              '提醒', {
                confirmButtonText: '知道了'
              }
            )
          }
        },
        // 复制代码
        copyCode() {
          let textarea = this.$refs.pluginDiyCode.$refs.textarea
          textarea.select()
          document.execCommand('Copy')
          this.$message({
            duration: 2000,
            message: '复制成功',
            type: 'success'
          })
        },
        // 获取自定义位置代码
        async renderCodeJs() {
          let {
            data
          } = await window._4paradigm_plug_API.getRenderCodeJs({
            sceneId: this.baseConfig.id,
            plug: true
          })
          if (data && data.renderJs) {
            this.pluginDiyCode = data.renderJs
          }
        },
        // 切换页面位置
        changePageLocation() {
          if (this.baseConfig.pageLocation === 'S') {
            // 选中侧边栏
            this.$alert(
              '推荐栏上线后，请进入网站后台“外观-->小工具”，将推荐栏小工具设置到侧边栏位置处',
              '提醒', {
                confirmButtonText: '知道了'
              }
            )
          } else if (this.baseConfig.pageLocation === 'A_L_B') {
            // 选中侧边栏
            this.$alert(
              '推荐栏上线后，请进入网站后台“外观-->小工具”，将推荐栏小工具设置到首页文章列表下方',
              '提醒', {
                confirmButtonText: '知道了'
              }
            )
          }
          this.baseConfig.showType = ShowTypeEnum.FIXED;
          this.changeShowType();
        },
        changeShowType() {
          if (this.baseConfig.showType === ShowTypeEnum.FEED) {
            this.baseConfig.typeSet = TypeSetEnum.AUTO
          } else {
            this.baseConfig.typeSet = TypeSetEnum.ONLY_TITLE
          }
        },
        async setTypeSet(key) {
          this.baseConfig.typeSet = key
          if (
            (this.baseConfig.typeSet === TypeSetEnum.L_IMG_R_TXT ||
              this.baseConfig.typeSet === TypeSetEnum.UP_IMG_DOWN_TXT ||
              this.baseConfig.typeSet === TypeSetEnum.UP_TXT_DOWN_IMG ||
              this.baseConfig.typeSet === TypeSetEnum.GROUP_PHOTO) &&
            this.judgeContentImgFlg
          ) {
            let {
              code
            } = await window._4paradigm_plug_API.checkPlugMate({
              sceneId: this.baseConfig.id || 0,
              plugSiteId: document.getElementById('paradigm_sitId') && document.getElementById(
                'paradigm_sitId').getAttribute(
                'value') || ''
            })
            if (code === 3702) {
              // 没有物料库
              this.pageAlert('noMate')
            } else if (code === 3703) {
              // 物料库中没有图片的物料
              this.pageAlert('noFitMate')
            }
          }
        },
        // type noMate,不存在物料库或是为登录；noFitMate 物料库中没有图片的物料
        pageAlert(type) {
          const msg = {
            noMate: '配置图文样式，请确保网站有足够图文内容推荐，否则会影响推荐结果展示',
            noFitMate: '内容库中无带图内容，请选择其他显示样式，否则可能无法展示推荐结果'
          }
          this.$alert(msg[type], '提醒', {
            confirmButtonText: '知道了'
          })
          this.judgeContentImgFlg = false
        }
      },

    });
  </script>

</body>

</html>