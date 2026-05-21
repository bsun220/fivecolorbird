<template>
    <div>
<!--        <el-card class="!border-none mb-4" shadow="never">
            <el-form
                class="mb-[-16px]"
                :model="queryParams"
                inline
            >
                <el-form-item label="统计月份" prop="statistical_month">
                    <el-date-picker
                        v-model="queryParams.statistical_month"
                        type="month"
                        format="YYYY年MM月"
                        value-format="YYYY-MM"
                        placeholder="选择统计月份">
                    </el-date-picker>
                </el-form-item>

                <el-form-item label="预计发放日期" prop="issue_date">
                    <daterange-picker
                        v-model:startTime="queryParams.start_time"
                        v-model:endTime="queryParams.end_time"
                        type="daterange"
                        range-separator="至"
                        start-placeholder="开始日期"
                        end-placeholder="结束日期"
                    />
                </el-form-item>


                <el-form-item label="工作评分" prop="work_score" class="w-[280px]">
                    <el-select class="w-[280px]" v-model="queryParams.work_score" clearable placeholder="请选择工作评分">
                        <el-option label="全部" value=""></el-option>
                        <el-option
                            v-for="(item, index) in dictData.work_score_type"
                            :key="index"
                            :label="item.name"
                            :value="item.value"
                        />
                    </el-select>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>-->
        <el-card class="!border-none" v-loading="pager.loading" shadow="never">

            <div class="mt-4">
                <el-table :data="pager.lists" >
                    <el-table-column label="统计月份" prop="statistical_month" width="110" :formatter="(_,__,v) => formatMonthText(v)" show-overflow-tooltip />
                    <el-table-column label="工作评分" prop="work_score" width="80">
                        <template #default="{ row }">
                            <dict-value :options="dictData.work_score_type" :value="row.work_score" />
                        </template>
                    </el-table-column>
                    <el-table-column label="工作点评" prop="work_comment" width="90" show-overflow-tooltip>
                        <template #default="{row}">
                            <div class="flex items-center">
                                <el-popover
                                    placement="bottom-start"
                                    :width="Math.min(400, row.work_comment.length * 10 + 50)"
                                    trigger="click"
                                    popper-class="reward-note-popover"
                                >
                                    <template #reference>
                                        <el-link
                                            type="primary"
                                            :underline="false"
                                            class="view-link"
                                            v-show="row.work_comment.length > 0"
                                        >
                                            查看说明
                                        </el-link>
                                    </template>
                                    <div class="p-2 max-h-[300px] overflow-auto">
                                        <pre class="whitespace-pre-wrap font-sans">{{ row.work_comment }}</pre>
                                    </div>
                                </el-popover>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="绩效工资" prop="merit_pay" width="100" show-overflow-tooltip />
                    <el-table-column label="绩效说明" prop="merit_pay_note" width="90" show-overflow-tooltip>
                        <template #default="{row}">
                            <div class="flex items-center">
                                <el-popover
                                    placement="bottom-start"
                                    :width="Math.min(400, row.merit_pay_note.length * 10 + 50)"
                                    trigger="click"
                                    popper-class="reward-note-popover"
                                >
                                    <template #reference>
                                        <el-link
                                            type="primary"
                                            :underline="false"
                                            class="view-link"
                                            v-show="row.merit_pay_note.length > 0"
                                        >
                                            查看说明
                                        </el-link>
                                    </template>
                                    <div class="p-2 max-h-[300px] overflow-auto">
                                        <pre class="whitespace-pre-wrap font-sans">{{ row.merit_pay_note }}</pre>
                                    </div>
                                </el-popover>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="发放日期" prop="issue_date" width="110" show-overflow-tooltip />
                    <el-table-column label="累计绩效" prop="cumulative_merit_pay" width="100" show-overflow-tooltip />
                    <el-table-column label="已发绩效" prop="issued" width="90" show-overflow-tooltip />
                  <el-table-column label="奖励金额" prop="reward_amount" width="90" show-overflow-tooltip />
                  <el-table-column label="奖励说明" prop="reward_amount_note" width="90" show-overflow-tooltip>
                    <template #default="{row}">
                      <div class="flex items-center">
                        <el-popover
                                    placement="bottom-start"
                                    :width="Math.min(400, row.reward_amount_note.length * 10 + 50)"
                                    trigger="click"
                                    popper-class="reward-note-popover"
                                >
                          <template #reference>
                            <el-link
                                            type="primary"
                                            :underline="false"
                                            class="view-link"
                                            v-show="row.reward_amount_note.length > 0"
                                        >
                              查看说明
                            </el-link>
                          </template>
                          <div class="p-2 max-h-[300px] overflow-auto">
                            <pre class="whitespace-pre-wrap font-sans">{{ row.reward_amount_note }}</pre>
                          </div>
                        </el-popover>
                      </div>
                    </template>
                  </el-table-column>

                  <el-table-column label="处罚金额" prop="penalty_amount" width="90" show-overflow-tooltip />
                  <el-table-column label="处罚说明" prop="penalty_amount_note" width="90" show-overflow-tooltip>
                    <template #default="{row}">
                      <div class="flex items-center">
                        <el-popover
                                    placement="bottom-start"
                                    :width="Math.min(400, row.penalty_amount_note.length * 10 + 50)"
                                    trigger="click"
                                    popper-class="reward-note-popover"
                                >
                          <template #reference>
                            <el-link
                                            type="primary"
                                            :underline="false"
                                            class="view-link"
                                            v-show="row.penalty_amount_note.length > 0"
                                        >
                              查看说明
                            </el-link>
                          </template>
                          <div class="p-2 max-h-[300px] overflow-auto">
                            <pre class="whitespace-pre-wrap font-sans">{{ row.penalty_amount_note }}</pre>
                          </div>
                        </el-popover>
                      </div>
                    </template>
                  </el-table-column>
                  <el-table-column label="剩余加班工时" prop="remaining_overtime_hours" width="130" show-overflow-tooltip />

                </el-table>
            </div>
            <div class="flex mt-4 justify-end">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>

    </div>
</template>

<script lang="ts" setup name="performanceLists">
import { usePaging } from '@/hooks/usePaging'
import { useDictData } from '@/hooks/useDictOptions'
import { apiPerformanceLists } from '@/api/performance'
import { formatMonthText } from '@/utils/util'

// 查询条件
const queryParams = reactive({
    user_id: '',
    statistical_month: '',
    merit_pay: '',
    merit_pay_note: '',
    issue_date: '',
    cumulative_merit_pay: '',
    issued: '',
    reward_amount: '',
    reward_amount_note: '',
    penalty_amount: '',
    penalty_amount_note: '',
    work_score: '',
    work_comment: '',
    start_time: '',
    end_time: '',
    type:1,
})



// 表格选择后回调事件
const handleSelectionChange = (val: any[]) => {
    selectData.value = val.map(({ id }) => id)
}

// 获取字典数据
const { dictData } = useDictData('work_score_type')

// 分页相关
const { pager, getLists, resetParams, resetPage } = usePaging({
    fetchFun: apiPerformanceLists,
    params: queryParams
})




getLists()
</script>

