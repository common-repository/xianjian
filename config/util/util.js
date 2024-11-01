// const deepAssign = require("deep-assign");
const ShowTypeEnum = {
  FEED: 1,
  SIDE: 2,
  FIXED: 3
};

const TypeSetEnum = {
  AUTO: 1, // 自动排版
  L_IMG_R_TXT: 2, // 左图右文
  UP_TXT_DOWN_IMG: 3, // 上文下图
  UP_IMG_DOWN_TXT: 4, // 上图下文
  TITLE_ABSTRACT: 5, // 标题摘要
  ONLY_TITLE: 6, // 纯标题
  GROUP_PHOTO: 7, // 组图
  L_IMG_R_TA: 8, // 左图右标题摘要
  L_TITLE_R_TIME: 9 // 左标题右时间
};

function deepAssignUtil(target) {
  function toObject(val) {
    if (val === null || val === undefined) {
      throw new TypeError("Sources cannot be null or undefined");
    }
    return Object(val);
  }
  function isObject(val) {
    return (
      val != null && typeof val === "object" && Array.isArray(val) === false
    );
  }
  function assignKey(to, from, key) {
    var val = from[key];
    if (val === undefined || val === null) {
      return;
    }
    if (hasOwnProperty.call(to, key)) {
      if (to[key] === undefined || to[key] === null) {
        throw new TypeError(
          "Cannot convert undefined or null to object (" + key + ")"
        );
      }
    }
    if (!hasOwnProperty.call(to, key) || !isObject(val)) {
      to[key] = val;
    } else {
      to[key] = assign(Object(to[key]), from[key]);
    }
  }
  function assign(to, from) {
    if (to === from) {
      return to;
    }
    from = Object(from);
    for (var key in from) {
      if (hasOwnProperty.call(from, key)) {
        assignKey(to, from, key);
      }
    }
    if (Object.getOwnPropertySymbols) {
      var symbols = Object.getOwnPropertySymbols(from);
      for (var i = 0; i < symbols.length; i++) {
        if (propIsEnumerable.call(from, symbols[i])) {
          assignKey(to, from, symbols[i]);
        }
      }
    }
    return to;
  }
  target = toObject(target);
  for (var s = 1; s < arguments.length; s++) {
    assign(target, arguments[s]);
  }
  return target;
}
function getDefaulRenderConfig(channel) {
  let defaultAdsRenderConfig = {
    // 产品类型，默认为web端
    productType: "web",
    // 自适应宽度
    isAutoWidth: false,
    isOfficialPlug: true,
    // 新logo显示（创建的新的场景，需要显示先荐新的logo）
    isFirstClickLogo: true,
    isImgSeted: false,
    selector: "article-render",
    isOptimizeReadingCount: true,
    isReadingCountEllipsis: true,
    box: {
      title: "",
      showType: ShowTypeEnum.FEED,
      typeSet: TypeSetEnum.AUTO,
      openType: 1,
      rowNum: 2,
      columnNum: 1,
      size: {
        width: 360,
        paddingTop: 12,
        paddingRight: 12,
        name: "sizeDef"
      },
      background: {
        isShow: true,
        color: "#FFFFFF",
        name: "backgroundDef"
      },
      border: {
        isShow: false,
        color: "#d5d9dd",
        type: "Whole",
        name: "borderDef",
        width: 1,
        style: "solid"
      }
    },
    titleBar: {
      modifier: {
        color: "#7F8FA4",
        type: "None"
      },
      size: {
        height: 40,
        name: "sizeDef"
      },
      font: {
        family: channel === "plugin_official_beepress" ? "" : "微软雅黑",
        size: 16,
        color: "#7F8FA4",
        name: "fontDef",
        bold: false,
        italic: false,
        textDecoration: false
      },
      border: {
        isShow: true,
        color: "#F5F5F5",
        type: "None",
        name: "borderDef",
        width: 1,
        style: "solid"
      },
      background: {
        isShow: false,
        color: "#F5F5F5",
        name: "backgroundDef"
      }
    },
    articleTitle: {
      font: {
        family:  channel === "plugin_official_beepress" ? "" :"微软雅黑",
        lineNum: 2,
        lineHeight: 18,
        size: 12,
        color: "#000000", // TODO
        name: "fontDef",
        bold: false,
        italic: false,
        textDecoration: false
      },
      activeFont: {
        color: "#000000", // TODO
        textDecoration: false,
        name: "fontDef",
        bold: false,
        italic: false
      },
      hoverFont: {
        color: "#000000",
        textDecoration: false,
        name: "fontDef",
        bold: false,
        italic: false
      },
      typeSetMark: {
        isShow: false,
        color: "#000000",
        size: {
          width: 5,
          paddingRight: 6
        }
      }
    },
    articleAbstract: {
      font: {
        family:  channel === "plugin_official_beepress" ? "" :"微软雅黑",
        lineNum: 2,
        lineHeight: 18,
        size: 12,
        color: "#9B9B9B",
        name: "fontDef",
        bold: false,
        italic: false,
        textDecoration: false
      }
    },
    articleHelper: {
      font: {
        name: "fontDef",
        family:  channel === "plugin_official_beepress" ? "" :"微软雅黑",
        lineHeight: 12,
        size: 12
      },
      // 默认只显示发布者和发布时间
      // 'helperPositions': ['publisherId', 'publishTime', 'label', 'readingCount'],
      helperPositions: [],
      publisherId: {
        name: "publisherId",
        toward: "Left",
        font: {
          color: "#9B9B9B"
        }
      },
      publishTime: {
        name: "publishTime",
        toward: "Left",
        font: {
          color: "#9B9B9B"
        },
        dateFormateType: ["intelligence", "yyyy-MM-dd"]
      },
      label: {
        name: "label",
        toward: "Left",
        count: 1,
        font: {
          color: "#7F8FA4"
        },
        border: {
          name: "borderDef",
          isShow: true,
          color: "#f5f5f5",
          type: "None",
          width: 1,
          style: "solid"
        },
        background: {
          name: "backgroundDef",
          isShow: true,
          color: "#D5D9DD"
        }
      },
      readingCount: {
        font: {
          color: "#9B9B9B"
        },
        name: "readingCount",
        toward: "Left"
      }
    },
    feedCfg: {
      loadNum: 10,
      autoLoad: true
    },
    carouselCfg: {
      carouselFlg: 1,
      carouselPages: 2,
      refreshBtn: {
        font: {
          size: 14,
          color: "#979797",
          name: "fontDef",
          bold: false,
          italic: false,
          textDecoration: false
        },
        background: {
          isShow: false,
          color: "#16C4C6",
          name: "backgroundDef"
        },
        border: {
          isShow: false,
          type: "Whole",
          color: "#F5F5F5",
          name: "borderDef",
          width: 1,
          style: "solid"
        }
      }
    },
    articleImg: {
      size: {
        width: 104,
        height: 58
      },
      border: {
        radius: 0
      }
    },
    articleRowDis: {
      dis: 12,
      border: {
        isShow: false,
        type: "None",
        color: "#E5E9EF",
        name: "borderDef",
        width: 1,
        style: "solid"
      }
    }
  };
  return defaultAdsRenderConfig;
}
window._4paradigm_plug_util = {
  deepAssign(...target) {
    return deepAssignUtil(...target);
  },
  ShowTypeEnum: ShowTypeEnum,
  TypeSetEnum: TypeSetEnum,

  getDefaultPlugInConfig(showType, typeSet) {
    let rowNum = 5;
    if (showType === ShowTypeEnum.FIXED) {
      // 固定位置
      if (
        typeSet === TypeSetEnum.UP_IMG_DOWN_TXT ||
        typeSet === TypeSetEnum.UP_TXT_DOWN_IMG
      ) {
        rowNum = 2;
      }
    }
    return {
      productType: "plugIn",
      isAutoWidth: true,
      isOfficialPlug: true,
      box: {
        title: "猜你喜欢",
        showType: showType,
        typeSet: typeSet,
        rowNum: rowNum,
        columnNum: 1
      },
      articleHelper: {
        helperPositions:
          typeSet === TypeSetEnum.L_TITLE_R_TIME ? ["publishTime"] : []
      },
      carouselCfg: {
        carouselFlg: 2
      }
    };
  },
  // 获取默认的插件配置
  getDefualtPlugInConfigbyTypeSet(channel, showType, typeSet) {
    let defaultConfig = getDefaulRenderConfig(channel);
    this.deepAssign(
      defaultConfig,
      this.getDefaultPlugInConfig(showType, typeSet)
    );
    return defaultConfig;
  },
  getParentDomain() {
    var url = window.location.href;
    var urlReg = /(http|https):\/\/([^\\/]+)\//i;
    const domain = url.match(urlReg);
    return domain != null && domain.length > 0 ? domain[0] : "";
  },
  getQueryVariable(variable) {
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i = 0; i < vars.length; i++) {
      var pair = vars[i].split("=");
      if (pair[0] == variable) {
        return pair[1];
      }
    }
    return false;
  },
  throttle(method, delay) {
    return function() {
      var context = this;
      var args = arguments;
      clearTimeout(method.timeoutId);
      method.timeoutId = setTimeout(function() {
        method.apply(context, args);
      }, delay);
    };
  },
  bindResizeWindow(bindFunc) {
    const throttleFresh = this.throttle(bindFunc, 100);
    window.onresize = throttleFresh;
  },
  unbindResizeWindow() {
    window.onresize = null;
  },
  // TODO 二分搜索， 并且完善测试和corner case检测
  tclamp(config, node, array, finalHeight) {
    const oldStyleHeight = node.style.height;
    node.style.height = "auto";
    node.style.maxHeight = finalHeight + 1 + "px";

    function clampSet() {
      setTimeout(() => {
        if (array.length > 0 && node.offsetHeight > finalHeight) {
          array.pop();
          clampSet();
        } else {
          node.style.height = oldStyleHeight;
          node.style.visibility = "visible";
        }
      }, 0);
    }
    if (array.length > 0 && node.offsetHeight > finalHeight) {
      clampSet();
      config.isShowMore = true;
    } else {
      node.style.height = oldStyleHeight;
      config.isShowMore = false;
      node.style.visibility = "visible";
    }
  },
  /*
   * 用于生成范围begin~end的自然数数组
   * @param begin: number
   * @param end: number
   * @return Array
   */
  arrayRange(begin, end) {
    if (!begin || !end) return [];
    const dis = end - begin + 1;
    return [...Array(dis).keys()].map(n => n + begin);
  },
  /*
   * 有大量的页面都有获取日期的需求，这里抽取一个函数来方便获取日期
   * @params offset: number 相对于今天的日期偏移，比如1为明天，-1为昨天
   * @return Date 日期
   */
  getDate(offset) {
    if (isNaN(offset)) offset = 0;
    const date = new Date();
    date.setDate(date.getDate() + offset);
    return date;
  },
  /**
   * 对日期进行格式化
   * @params date: Date
   * @return string 以yyyy-mm-dd格式标识的日期字符串
   */
  formatDate(date) {
    let y = date.getFullYear();
    let m = String(date.getMonth() + 1).padStart(2, "0");
    let d = String(date.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
  }
};
