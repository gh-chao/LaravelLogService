<template>
    <div id="app">
        <div class="wrap">
            <div class="serachNav">
                <el-input @keyup.enter.native="fetchData" placeholder="请输入关键字" v-model="request.query"
                          clearable></el-input>
                <div class="serachInput">
                    <el-select v-model="request.type" placeholder="请选择" @change="changeType">
                        <el-option v-for="item in options" :key="item.value" :label="item.label"
                                   :value="item.value"></el-option>
                    </el-select>
                </div>
                <!--<div class="dateTime">-->
                    <!--<el-date-picker v-model="request.dateTimeRange" :disabled="isDisabled" type="datetimerange"-->
                                    <!--range-separator="至"-->
                                    <!--start-placeholder="开始日期" end-placeholder="结束日期"></el-date-picker>-->
                <!--</div>-->
                <div class="serachBtn">
                    <el-button type="primary" icon="el-icon-search" @click="fetchData">搜索</el-button>
                </div>
            </div>
            <p>日志总条数:<strong>{{ response.total }}</strong>条&nbsp;&nbsp;&nbsp;&nbsp;查询状态:<span>结果精确</span></p>
            <div class="journal">
                <el-menu default-active="1" class="el-menu-demo" mode="horizontal">
                    <el-menu-item index="1">原始日志</el-menu-item>
                </el-menu>
                <el-table v-loading="request.loading" :data="response.logs" border>
                    <el-table-column prop="__source__" label="source" width="180"></el-table-column>
                    <el-table-column prop="time" sortable label="时间" width="180"></el-table-column>
                    <el-table-column prop="channel" label="channel" width="180"></el-table-column>
                    <el-table-column prop="level" label="level" width="200"></el-table-column>
                    <el-table-column prop="message" label="message"></el-table-column>
                    <el-table-column prop="context" label="context" width="200">
                        <template slot-scope="scope">{{scope.row.context|capitalize}}</template>
                    </el-table-column>
                    <el-table-column fixed="right" label="操作" width="80">
                        <template slot-scope="scope">
                            <el-button @click="handleClick(scope.row)" type="text" size="small">查看</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="block">
                <el-pagination @current-change="fetchData" :current-page.sync="request.page" :page-size="10"
                               prev-text="上一页"
                               next-text="下一页" layout="prev, jumper, next" :total="response.total"></el-pagination>
            </div>
        </div>
        <el-dialog title="Context" :visible.sync="contextVisible" width="60%" center>
      <span>
				<vue-json-pretty
                    :path="'res'"
                    :data="contextContent"
                    @click="handleClick">
		    </vue-json-pretty>
      </span>
            <span slot="footer" class="dialog-footer">
        <el-button @click="contextVisible = false">关闭</el-button>
      </span>
        </el-dialog>
    </div>
</template>
<script>
    import axios from 'axios'
    import addDays from 'date-fns/add_days'
    import addMinutes from 'date-fns/add_minutes'
    import VueJsonPretty from 'vue-json-pretty'
    import format from 'date-fns/format'

    const TYPE_MINUTE = 'minute'
    const TYPE_FIFTEEN_MINUTE = 'fifteen_minute'
    const TYPE_HOUR = 'hour'
    const TYPE_FOUR_HOUR = 'four_hour'
    const TYPE_DAY = 'day'
    const TYPE_WEEK = 'week'
    const TYPE_CUSTOM = 'custom'

    export default {
        data() {
            return {
                options: [
                    {
                        value: TYPE_MINUTE,
                        label: '1分钟'
                    },
                    {
                        value: TYPE_FIFTEEN_MINUTE,
                        label: '15分钟'
                    },
                    {
                        value: TYPE_HOUR,
                        label: '1小时'
                    },
                    {
                        value: TYPE_FOUR_HOUR,
                        label: '4小时'
                    },
                    {
                        value: TYPE_DAY,
                        label: '1天'
                    },
                    {
                        value: TYPE_WEEK,
                        label: '1周'
                    },
                    // {
                    //     value: TYPE_CUSTOM,
                    //     label: '自定义'
                    // }
                ],

                contextVisible: false,
                contextContent: null,

                request: {
                    loading: false,
                    type: TYPE_FIFTEEN_MINUTE,
                    query: '',
                    page: 1,
                    dateTimeRange: []
                },
                response: {
                    logs: [],
                    total: 0
                },

                isDisabled: true
            }
        },
        components: {
            VueJsonPretty
        },
        created: function () {
            this.fetchData()
        },
        filters: {// 过滤器
            capitalize: function (value) {
                if (value) {
                    return value.substring(0, 20)
                }
            }
        },
        methods: {
            changeType(type) {
                this.request.type = type;
                this.fetchData()
            },
            dateFormat(date) {
                return format(date, 'YYYY-MM-DD HH:mm:ss')
            },

            fetchData() {
                const that = this
                this.request.loading = true

                const dateRange = this.getDateRange(this.request.type);

                axios.post(Window.API, {
                    query: this.request.query,
                    page: this.request.page,
                    begin: this.dateFormat(dateRange[0]),
                    end: this.dateFormat(dateRange[1])
                }, {
                    headers: {'X-Requested-With': 'XMLHttpRequest'},
                    withCredentials: true,
                    params: {
                        t: Math.random()
                    }
                }).then((response) => {
                    that.response.logs = response.data.data
                    that.response.total = response.data.meta.pagination.total
                    that.request.loading = false
                })
            },
            handleClick(row) {
                this.contextVisible = true
                this.contextContent = JSON.parse(row.context)
            },


            getDateRange(type) {
                let date = new Date();
                switch (this.request.type) {
                    case TYPE_MINUTE:
                        return [addMinutes(date, -1), date];
                    case TYPE_FIFTEEN_MINUTE:
                        return [addMinutes(date, -15), date];
                    case TYPE_HOUR:
                        return [addMinutes(date, -60), date];
                    case TYPE_FOUR_HOUR:
                        return [addMinutes(date, -240), date];
                    case TYPE_DAY:
                        return [addDays(date, -1), date];
                    case TYPE_WEEK:
                        return [addDays(date, -7), date];
                    case TYPE_CUSTOM:
                    default:
                        return [addDays(date, -15), date];
                }

            }

            // loadType(type) {
            //     let date = new Date() // 获取当前时间
            //     this.isDisabled = this.request.type !== TYPE_CUSTOM
            //
            //     switch (this.request.type) {
            //         case TYPE_MINUTE:
            //             this.setDateRange(addMinutes(date, -1), date)
            //             break
            //         case TYPE_FIFTEEN_MINUTE:
            //             this.setDateRange(addMinutes(date, -15), date)
            //             break
            //         case TYPE_HOUR:
            //             this.setDateRange(addMinutes(date, -60), date)
            //             break
            //         case TYPE_FOUR_HOUR:
            //             this.setDateRange(addMinutes(date, -240), date)
            //             break
            //         case TYPE_DAY:
            //             this.setDateRange(addDays(date, -1), date)
            //             break
            //         case TYPE_WEEK:
            //             this.setDateRange(addDays(date, -7), date)
            //             break
            //         case TYPE_CUSTOM:
            //         default:
            //     }
            //
            //     this.fetchData()
            // }
        }
    }
</script>
<style>
    html,
    body,
    #app {
        height: 96%;
        padding: 1%;
    }

    * {
        margin: 0;
        padding: 0;
    }

    #app {
        font-family: 'Avenir', Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        color: #2c3e50;
    }

    .wrap {
        height: 100%;
        flex-direction: column;
        display: -webkit-flex;
    }

    .serachNav {
        display: flex;
    }

    .journal {
        -webkit-box-flex: 1;
        -webkit-flex: 1;
        flex: 1;
        overflow: auto;
    }

    .block {
        text-align: right;
        margin-top: 10px;
    }

    .serachBtn,
    .serachInput,
    .dateTime {
        margin-left: 5px;
    }

    .el-menu-item,
    .is-active {
        color: #409EFF !important;
    }

    .el-table__header {
        table-layout: !important;
    }

    .el-pagination__jump {
        margin-left: 0;
    }

    .el-message-box__message p {
        word-wrap: break-word;
        word-break: break-all;
    }

    .btn-prev span,
    .btn-next span {
        background: #eee;
        padding: 0 5px;
        border-radius: 5px;
    }

    a {
        color: #42b983;
    }

    p {
        text-align: center;
        font-size: 12px;
        padding-top: 10px;
        text-align: center;
    }
</style>
