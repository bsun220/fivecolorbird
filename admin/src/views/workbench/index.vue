<template>
    <div class="workbench">
        <!-- 顶部区域：用户信息和日历 -->
        <div class="lg:flex gap-4">
            <!-- 用户信息卡片 -->
            <el-card class="!border-none mb-4 lg:w-[800px]" shadow="never">
                <template #header>
                    <div class="flex flex-col w-full">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold">{{workbenchData.userinfo.time_text}}！{{workbenchData.userinfo.username}}。</h2>
                        </div>
                        <div class="flex mt-2 gap-2">
                            <div v-for="tag in [workbenchData.userinfo.dept_name, workbenchData.userinfo.jobs_name]"
                                 :key="tag"
                                 class="bg-[#EBF0F5] px-3 py-1 rounded-md">
                                <span class="text-sm text-gray-700">{{tag}}</span>
                            </div>
                        </div>
                    </div>
                </template>

                <h3 class="text-gray-600 mb-4">常用功能</h3>
                <div class="grid grid-cols-4 md:grid-cols-8 gap-4">
                    <router-link
                        v-for="item in workbenchData.menu"
                        :key="item.id"
                        :to="item.url"
                        class="flex flex-col items-center group"
                    >
                        <image-contain
                            width="48px"
                            height="48px"
                            :src="item?.image"
                            class="group-hover:scale-110 transition-transform duration-200"
                        />
                        <span class="mt-2 text-sm group-hover:text-primary group-hover:font-medium">
              {{ item.name }}
            </span>
                    </router-link>
                </div>
            </el-card>

            <!-- 日历卡片 -->
            <el-card class="!border-none mb-4 flex-1" shadow="never">
                <template #header>
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">{{ currentMonth }}月</h2>
                        <el-button-group>
                            <el-button size="small" @click="prevMonth">
                                <el-icon><arrow-left /></el-icon>
                            </el-button>
                            <el-button size="small" @click="nextMonth">
                                <el-icon><arrow-right /></el-icon>
                            </el-button>
                            <el-button size="small" @click="goToday">今天</el-button>
                        </el-button-group>
                    </div>
                </template>
                <!-- 优化后的日历组件 -->
                <el-calendar v-model="calendarDate" class="optimized-calendar">
                    <template #date-cell="{ data }">
                        <div class="calendar-cell">
                            <div class="day-number" :class="{
                'is-today': data.isCurrentDay,
                'is-weekend': data.day.split('-')[2] === '0' || data.day.split('-')[2] === '6'
              }">
                                {{ data.day.split('-')[2] }}
                            </div>
                        </div>
                    </template>
                </el-calendar>
            </el-card>
        </div>

        <!-- 底部区域：三个功能卡片 -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
            <!-- 绩效卡片 -->
            <el-card class="!border-none" shadow="never">
                <div class="grid grid-cols-2 gap-4 p-3">
                    <div v-for="(item, index) in performanceItems" :key="index"
                         class="bg-gray-50 p-3 rounded-lg text-center hover:bg-gray-100 transition-colors">
                        <div class="text-lg font-bold text-gray-800">{{ item.value }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ item.label }}</div>
                        <router-link :to="item.link" class="text-xs text-blue-500 hover:underline mt-2 block">查看</router-link>
                    </div>
                </div>
            </el-card>

            <!-- 系统消息卡片 -->
            <el-card class="!border-none" shadow="never">
                <template #header>
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">系统消息</h3>
                        <router-link to="/message/system_msg" class="text-sm text-blue-500 hover:underline">查看更多</router-link>
                    </div>
                </template>
                <div class="space-y-3">
                    <div v-for="item in workbenchData.system_msg" :key="item.id"
                         class="flex justify-between py-2 border-b border-gray-100 last:border-0">
                        <p class="text-gray-700 text-sm flex-1 pr-2">{{item.content}}</p>
                        <span class="text-gray-400 text-xs whitespace-nowrap">{{formatDate(item.create_time)}}</span>
                    </div>
                </div>
            </el-card>

            <!-- 最新公告卡片 -->
            <el-card class="!border-none" shadow="never">
                <template #header>
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">最新公告</h3>
                        <router-link to="/message/notices" class="text-sm text-blue-500 hover:underline">查看</router-link>
                    </div>
                </template>
                <div class="space-y-2">
                    <p class="text-gray-500 text-sm">TO: {{workbenchData.notice.dept_name}}</p>
                    <p class="text-gray-700 text-sm line-clamp-3">{{workbenchData.notice.content}}</p>
                    <div class="flex justify-between pt-2 mt-2 border-t border-gray-100">
                        <span class="text-gray-400 text-xs">管理部</span>
                        <span class="text-gray-400 text-xs">{{formatDate(workbenchData.notice.create_time)}}</span>
                    </div>
                </div>
            </el-card>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref, computed, reactive, onMounted } from 'vue'
import { ArrowLeft, ArrowRight } from '@element-plus/icons-vue'
import { getWorkbench } from '@/api/app'
import useSettingStore from '@/stores/modules/setting'

const calendarDate = ref(new Date())
const settingStore = useSettingStore()

// 当前月份（补零显示）
const currentMonth = computed(() => {
    const month = calendarDate.value.getMonth() + 1
    return month < 10 ? `0${month}` : month
})

// 上个月
const prevMonth = () => {
    const date = new Date(calendarDate.value)
    date.setMonth(date.getMonth() - 1)
    calendarDate.value = date
}

// 下个月
const nextMonth = () => {
    const date = new Date(calendarDate.value)
    date.setMonth(date.getMonth() + 1)
    calendarDate.value = date
}

// 回到今天
const goToday = () => {
    calendarDate.value = new Date()
}

// 工作台数据
const workbenchData = reactive({
    menu: [],       // 功能菜单
    notice: {},     // 公告
    userinfo: {},   // 用户信息
    system_msg: [], // 系统消息
    performance: {} // 绩效数据
})

// 绩效数据项
const performanceItems = computed(() => [
    {
        value: workbenchData.performance.merit_pay || '0.00',
        label: '近期绩效奖金（元）',
        link: '/work/user/performance'
    },
    {
        value: workbenchData.performance.reward_amount || '0.00',
        label: '近期奖励（元）',
        link: '/work/user/performance'
    },
    {
        value: workbenchData.performance.cumulative_merit_pay || '0.00',
        label: '累计绩效奖金（元）',
        link: '/work/user/performance'
    },
    {
        value: workbenchData.performance.days_diff_rounded || '0',
        label: '在职时间（天）',
        link: '/'
    }
])

// 格式化日期
const formatDate = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString()
}

// 获取工作台数据
const getData = async () => {
    try {
        const res = await getWorkbench()
        Object.assign(workbenchData, res)
    } catch (err) {
        console.error('获取工作台数据失败:', err)
    }
}

onMounted(() => {
    getData()
})
</script>

<style scoped>
/* 日历样式 */
.optimized-calendar {
    --el-calendar-cell-width: auto;
    --el-calendar-cell-height: 80px;
}

.optimized-calendar :deep(.el-calendar__header) {
    display: none;
}

.optimized-calendar :deep(.el-calendar-table) {
    border: none;
    width: 100%;
    table-layout: fixed;
    border-collapse: separate;
    border-spacing: 4px;
}

.optimized-calendar :deep(.el-calendar-table thead th) {
    padding: 8px 0;
    color: var(--el-text-color-regular);
    font-weight: 500;
    text-align: center;
    font-size: 13px;
}

.optimized-calendar :deep(.el-calendar-table td),
.optimized-calendar :deep(.el-calendar-table th) {
    border: none;
    vertical-align: top;
    padding: 0;
}

.calendar-cell {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 6px 4px;
    border-radius: 8px;
    transition: all 0.2s;
}

.day-number {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 500;
    color: var(--el-text-color-regular);
    border-radius: 50%;
    transition: all 0.2s;
}

.is-today .day-number {
    background: linear-gradient(135deg, #E6F7FF 0%, #BAE7FF 100%);
    color: #1890FF;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(24, 144, 255, 0.2);
}

.is-weekend .day-number {
    color: var(--el-color-danger);
}

.optimized-calendar :deep(.el-calendar-table td.is-selected) {
    background-color: transparent;
}

.optimized-calendar :deep(.el-calendar-table .el-calendar-day:hover) {
    background-color: var(--el-color-primary-light-9);
    border-radius: 8px;
}

.calendar-cell:hover .day-number {
    transform: scale(1.1);
}

/* 响应式调整 */
@media (max-width: 1024px) {
    .optimized-calendar {
        --el-calendar-cell-height: 70px;
    }

    .day-number {
        font-size: 12px;
        width: 24px;
        height: 24px;
    }
}

@media (max-width: 768px) {
    .optimized-calendar {
        --el-calendar-cell-height: 60px;
    }
}
</style>
