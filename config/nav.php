<script>
    Vue.component('res-question-dialog', {
        name: 'ResQuestion',
        data() {
            return {
                showResQuestion: false,
                postQuestionLoadingFlg: false,
                questionData: {
                    cif: '',
                    info: ''
                },
                beerPlugFlg: false
            }
        },
        props: {},

        methods: {
            show() {
                this.showResQuestion = true
                this.questionData = {
                    cif: '',
                    info: ''
                }
            },
            hide() {
                this.showResQuestion = false

            },
            // 提交反馈
            postQuestion() {
                this.postQuestionLoadingFlg = true
                window._4paradigm_plug_API.plugFeedback({
                    phone: this.questionData.cif,
                    feedback: this.questionData.info
                }).then(({
                    code,
                    info,
                    data
                }) => {
                    if (code !== 200) {
                        this.$message.error(info)
                        this.postQuestionLoadingFlg = false
                        return
                    }
                    this.postQuestionLoadingFlg = false
                    this.showResQuestion = false
                    this.$message.success('提交反馈成功')
                })
            }
        },
        template: `<el-dialog title="问题反馈" :visible.sync="showResQuestion"                       class="res-question-scope">
                        <div>
                            <div class="item-box">
                                <div class="item-title">手机号/微信/QQ</div>
                                <el-input placeholder='非必填，建议填写，能更加快速解决问题' v-model='questionData.cif' :maxlength="40">
                                </el-input>
                            </div>
                            <div class="item-box">
                                <div class="item-title">问题描述</div>
                                <el-input :rows="5" type="textarea" v-model="questionData.info" placeholder='必填，请描述您遇到的问题' :maxlength="200"></el-input>
                            </div>
                        </div>
                        <div slot="footer" class="dialog-footer">
                            <el-button @click="showResQuestion = false">取消</el-button>
                            <el-button type="primary" :disabled="!questionData.info" @click="postQuestion" :loading="postQuestionLoadingFlg">提交</el-button>
                        </div>
                </el-dialog>`
    });
    Vue.component('paradigm-nav', {
        data() {
            return {
                beerPlugFlg: false
            }
        },
        mounted() {
            const channel = document.getElementById('paradigm_plugChannel') && document.getElementById(
                'paradigm_plugChannel').getAttribute(
                'value') || '';
            this.beerPlugFlg = channel === 'plugin_official_beepress'
        },
        methods: {
            logoClick() {
                this.$emit('logo-click')
            },
            responseQuestion() {
                this.$refs.ResQuestionDialog.show()
            },
             // 强制重新抓取物料
            resetMate () {
                this.$confirm('您是否同意清除云端推荐数据并重新上传？', '提示', {
                    confirmButtonText: '同意',
                    cancelButtonText: '不同意',
                    type: 'error'
                })
                    .then(() => {
                        window._4paradigm_plug_API.plugDeleteItemset().then(({ code, info }) => {
                        if (code === 200) {
                        this.$message({
                            type: 'success',
                            message: '清除云端推荐数据成功!',
                            duration: 1500
                        })
                        paradigmPostResetMate({
                            isResetMate: 'true'
                        })
                        } else {
                        this.$message.error(info)
                        }
                    })
                    })
                    .catch(() => {})
            }
        },
        template: `<div class="_paradigm-pluIn-nav">
                        <div v-if='beerPlugFlg' class="_paradigm-pluIn-nav-title">内容推荐设置</div>
                        <img class="_paradigm-pluIn-nav-logo" src="<?php  echo plugins_url('static/img/easy-recom.svg', __FILE__) ?>"
                            @click='logoClick' v-else />
                        <a class="_paradigm-plug-doc-href" target="_blank" href='https://nbrecsys.4paradigm.com/static/wordpress%E5%85%88%E8%8D%90%E6%8F%92%E4%BB%B6%E4%BD%BF%E7%94%A8%E6%95%99%E7%A8%8B.pdf'
                        download="wordpress先荐插件使用教程.pdf">快速上手教程</a>
                        <div class="_paradigm-plug-doc-href" @click="responseQuestion()">问题反馈</div>
                        <el-tooltip class="item" effect="dark" content="如果网站内容更新、删除之后，先荐依旧推荐旧内容和删除的链接，建议您更新云端库。更新云端库会把云端数据清空并重新抓取网站内容，可能会有5-30分钟没有推荐结果，请耐心等待。" placement="bottom-end" popper-class='plug-tooltip'>
                        <div class="_paradigm-plug-doc-href" @click="resetMate()">更新云端库</div>
                        </el-tooltip>
                        <res-question-dialog ref="ResQuestionDialog"></res-question-dialog>
                 </div>`
    })
</script>