// 存入cookie
function saveToken(name, value) {
  let days = 7;
  let exp = new Date();
  exp.setTime(exp.getTime() + days * 24 * 60 * 60 * 1000);
  document.cookie =
    name + '=' + escape(value) + ';expires=' + exp.toGMTString();
}
//  取cookie
function getToken(name) {
  let reg = new RegExp('(^| )' + name + '=([^;]*)(;|$)');
  let arr = document.cookie.match(reg);
  if (arr) return unescape(arr[2]);
  return null;
}

axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';

// const baseURL = 'https://recsys-free.4paradigm.com';
const baseURL = "https://nbrecsys.4paradigm.com";
var _4paradigm_token = '';
function xhrError() {}
//加了这段就可以跨域了
let http = axios.create({ baseURL: baseURL });
function createHttp(http) {
  return {
    async get(url, params = {}) {
      if (url !== '/business/plug/token') {
        await getTokenByPlugSiteId();
        params.token = getToken('_4paradigm_token');
      }
      try {
        let { data } = await http.get(url, { params });
        return data;
      } catch (err) {
        xhrError();
      }
    },
    async post(url, params = {}, isRequestBody) {
      await getTokenByPlugSiteId();
      params.token = getToken('_4paradigm_token');
      let postParams = {};
      let config = {};
      if (isRequestBody) {
        url += '?token=' + params.token;
        postParams = params;
        config = {
          headers: {
            'Content-Type': 'application/json'
          }
        };
      } else {
        postParams = new URLSearchParams();
        Object.entries(params).forEach(param => {
          postParams.append(...param);
        });
        config = {
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          }
        };
      }
      try {
        let { data } = await http.post(url, postParams, config);
        return data;
      } catch (err) {
        xhrError();
      }
    }
  };
}
let apiT = createHttp(http);

// 根据插件id获取token
async function getTokenByPlugSiteId() {
  const plugSiteId =
    (document.getElementById('paradigm_sitId') &&
      document.getElementById('paradigm_sitId').getAttribute('value')) ||
    '';
  let { data } = await apiT.get('/business/plug/token', {
    plugSiteId: plugSiteId
  });
  if (data && data.token) {
    saveToken('_4paradigm_token', data.token);
  }
}
window._4paradigm_plug_API = {
  // 获取商家信息
  getUserInfo: params => apiT.get('/business/center', params),
  // 根据id获取场景详情
  getSceneDetail: params => {
    return apiT.get('/business/scene', params);
  },
  // 获取前端渲染配置
  getFeRender: params => {
    return apiT.get('/business/scene/render', params);
  },
  // 获取场景列表
  getSceneList: params => apiT.get('/business/scenes', params),

  // 保存场景接口
  plugSaveScene: params => {
    return apiT.post('/business/plug/scene', params);
  },
  // 校验物料样式展示状态
  checkPlugMate: params => {
    return apiT.get('/business/plug/scene/itemset/check', params);
  },
  // 获取报表接口
  getReport: params => apiT.get('/business/report/getMetricsValues', params),
  // 获取商家信息
  getUserInfo: params => apiT.get('/business/center', params),
  // 删除场景
  removeScene: params => {
    return apiT.post('/business/scene/remove', params);
  },
  // 获取渲染代码
  getRenderCodeJs: params => apiT.get('/business/scene/render/js', params),
  // 保存运营规则
  saveConfigRule: params => apiT.post('/business/scene/rules', params),
  // 获取物料一级分类
  getMateCategoryV1: params => apiT.get('/business/category_v1', params),
  // 获取过滤规则
  getFilterRule: params => apiT.get('/business/selection/getrule', params),
  // 保存过滤规则
  saveFilterRule: params =>
    apiT.post('/business/selection/saverulenew', params, true),
  // 获取符合过滤规则的条数
  getFilterItemNumber: params =>
    apiT.post('/business/selection/itemNumberNew', params, true),
  // 获取待选过滤规则
  getAllFilterRule: params => apiT.get('/business/selection/oldrules', params),
  // 删除过滤规则
  removeFilterRule: params =>
    apiT.get('/business/selection/removerule', params),
  // 场景下物料操作(置顶、必推、权重)
  materialRuleOpr: params => apiT.post('/business/item/single/rule', params),
  // 获取物料操作数量是否超过4000限制,是否可继续操作(供场景物料列表使用)
  getMatesOprCanSave: params => apiT.post('/business/item/rule/count', params),
  // 物料详情分页（场景物料详情页面）
  getSceneMaterisalDetail: params =>
    apiT.post('/business/scene/items/search', params),
  // 物料日志接口
  materialLog: params => apiT.get('/business/itemset/log', params),
  /*
   * 接口文档
   * https://wiki.4paradigm.com/pages/viewpage.action?pageId=32311638
   * 例子：GET /business/report/getMetricsValues?customID=98765&sceneID=12345&begin=2018-03-01&end=2018-03-07&metrics=showUV
   */
  getReportSceneShowUV: params => {
    params = Object.assign({}, params);
    params.metrics = 'showUV';
    return apiT.get('/business/report/getMetricsValues', params);
  },
  /*
   * 以下获取物料的接口文档
   * https://wiki.4paradigm.com/pages/viewpage.action?pageId=32311638
   * GET customID=12345&sceneID=56789&begin=2018-03-04&end=2018-03-06
   */
  // 加权项曝光分布（按物料集维度）
  getReportByItemWeight: params => {
    params = Object.assign({}, params);
    params.groupBy = 'weight';
    params.filter = 'weight:1';
    params.metrics = 'itemCount,showPV';
    return apiT.get('/business/report/getDistribution', params);
  },
  // 必推物料曝光（按物料集维度)
  getReportByItemAppear: params => {
    params = Object.assign({}, params);
    params.groupBy = 'itemID,url';
    params.filter = 'sticky:1';
    params.metrics = 'showPV,detailPageShowPV,ctr';
    return apiT.get('/business/report/getDistribution', params);
  },
  // 置顶物料曝光（按物料集维度）
  getReportByItemTop: params => {
    params = Object.assign({}, params);
    params.groupBy = 'itemID,url';
    params.filter = 'top:1';
    params.metrics = 'showPV,detailPageShowPV,ctr';
    return apiT.get('/business/report/getDistribution', params);
  },
  // 插件商家信息反馈
  plugFeedback: params => {
    return apiT.get('/business/plug/feedback', params);
  },
  // 物料禁封接口
  materialBan: params => apiT.post('/business/item/ban', params),
  // 绑定商家支付宝
  saveAlipayAccount: params => apiT.post('/ad/ssp/account/alipay', params),
  // 获取商家账号信息
  getAdAccount: params => apiT.get('/ad/ssp/account/detail', params),
  // 商家提现列表
  withdrawsList: params => apiT.get('/ad/ssp/account/withdraws', params),
  // 商家提现申请
  withdrawsApply: params => apiT.post('/ad/ssp/account/withdraw', params),
  // 场景广告开关
  sceneAdconfig: params => apiT.post('/ad/ssp/scene/adconfig', params),
  // 商家收入明细
  getIncomDetailList: params => apiT.get('/ad/ssp/incom/data', params),
  // 商家每天场景收入列表
  getIncomDetailListByScene: params =>
    apiT.get('/ad/ssp/incom/scene/data', params),
  // 商家每月收入
  getIncomMonthList: params => apiT.get('/ad/ssp/incom/month/data', params),
  // 获取广告场景
  getAdSceneList: params => apiT.get('/ad/ssp/adSceneData', params),
  // 插件清空物料库
  plugDeleteItemset: params => apiT.post('/business/itemset/delete', params)
};
