<template>
  <div class="bg-[#f7f8fa] min-h-screen">
    <!-- 顶部区域：头像、姓名、部门 -->
    <div class="flex items-center gap-6 mb-6 px-0">
      <img
        class="w-20 h-20 rounded-full object-cover border-4 border-white shadow"
        :src="userinfo.avatar || 'https://img1.baidu.com/it/u=123456789,123456789&fm=253&fmt=auto&app=138&f=JPEG?w=200&h=200'"
        alt="头像"
      />
      <div class="flex-1 min-w-0">
        <div class="flex items-end gap-3 flex-wrap">
          <span class="text-2xl font-bold text-gray-900 truncate">{{ userinfo.username }}</span>
          <span class="text-base text-gray-500">{{ userinfo.jobs_name }}</span>
          <span class="text-gray-300 font-bold">|</span>
          <span class="text-base text-gray-500">{{ userinfo.dept_name }}</span>
        </div>
      </div>
    </div>
    <!-- 公告栏 跑马灯效果 -->
    <div class="w-full px-0">
      <el-card class="mb-6 !border-none bg-[#f4f6fb]" shadow="never" body-style="padding: 12px 20px;">
        <div class="flex items-center gap-2 justify-start overflow-hidden">
          <!-- 喇叭icon -->

          <span class="w-8 h-8 flex items-center justify-center">
              <image-contain
                  width="18px"
                  height="16px"
                  :src="svg.gsgg"
                  class="group-hover:scale-110 transition-transform duration-200"
              />
          </span>
          <span class="font-bold text-gray-700">公司公告</span>
          <div class="flex-1 overflow-hidden relative">
            <marquee behavior="scroll" direction="left" scrollamount="5" class="text-blue-600 font-medium">
              {{ notice.title }}
            </marquee>
          </div>
        </div>
      </el-card>
    </div>
    <!-- 主体内容 -->
    <div class="w-full flex flex-col gap-6 px-0">
      <!-- 个人信息卡片 -->
      <el-card class="!border-none" shadow="never">
        <div class="mb-4 flex items-center gap-2 text-lg font-bold">
          <!-- 头像icon -->
            <span class="w-6 h-6 flex items-center justify-center bg-blue-100 rounded-full">
                 <image-contain
                     width="20px"
                     height="24px"
                     :src="svg.grzx"
                     class="group-hover:scale-110 transition-transform duration-200"
                 />
            </span>
          <span>个人信息</span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-y-3 gap-x-6 text-gray-700 text-[15px]">
          <div class="flex"><span class="text-gray-400 w-20">工号：</span><span>{{ userinfo.job_no }}</span></div>
          <div class="flex"><span class="text-gray-400 w-20">性别：</span><span>{{ userinfo.gender }}</span></div>
          <div class="flex"><span class="text-gray-400 w-20">手机号：</span><span>{{ userinfo.mobile }}</span></div>
          <div class="flex"><span class="text-gray-400 w-20">证件类型：</span><span>{{ userinfo.id_type }}</span></div>
          <div class="flex"><span class="text-gray-400 w-20">入职时间：</span><span>{{ userinfo.entry_date }}</span></div>
          <div class="flex"><span class="text-gray-400 w-20">任职岗位：</span><span>{{ userinfo.jobs_name }}</span></div>
          <div class="flex"><span class="text-gray-400 w-20">学历：</span><span>{{ userinfo.education }}</span></div>
          <div class="flex"><span class="text-gray-400 w-20">邮箱地址：</span><span>{{ userinfo.email }}</span></div>
          <div class="flex"><span class="text-gray-400 w-20">证件号：</span><span>{{ userinfo.id_number }}</span></div>
          <div class="flex"><span class="text-gray-400 w-20">在职状态：</span><span>{{ userinfo.status }}</span></div>
          <div class="flex"><span class="text-gray-400 w-20">所属部门：</span><span>{{ userinfo.dept_name }}</span></div>
          <div class="flex"><span class="text-gray-400 w-20">年龄：</span><span>{{ userinfo.age }}</span></div>
          <div class="flex"><span class="text-gray-400 w-20">联系地址：</span><span>{{ userinfo.address }}</span></div>
          <div class="flex"><span class="text-gray-400 w-20">月薪资¥：</span><span>{{ userinfo.salary }}</span></div>
        </div>
      </el-card>
      <!-- 绩效统计和消息通知并排 -->
      <div class="flex flex-col lg:flex-row gap-6 px-0">
        <!-- 绩效统计卡片 -->
        <el-card class="!border-none flex-1" shadow="never">
          <div class="mb-4 flex items-center gap-2 text-lg font-bold">
            <!-- 数据/图表icon -->
              <span class="w-6 h-6 flex items-center justify-center bg-blue-100 rounded-full">
                  <image-contain
                      width="20px"
                      height="24px"
                      :src="svg.jxtj"
                      class="group-hover:scale-110 transition-transform duration-200"
                  />
              </span>
            <span>绩效统计</span>
          </div>
          <div class="flex flex-col gap-2">
            <div v-for="item in performance" :key="item.statistical_month" class="flex items-center gap-6 text-[15px]">
              <span class="w-8 flex-shrink-0 flex justify-center whitespace-nowrap">
                <span class="block w-1 h-8 bg-blue-500 rounded-full mr-2"></span>
                <span class="ml-1 font-bold">{{ item.statistical_month.slice(-2) }}月</span>
              </span>
              <span>评分等级<span class="font-bold text-gray-900">{{ item.work_score_desc }}</span></span>
              <span>本月绩效奖金<span class="font-bold text-gray-900">{{ item.merit_pay }}</span></span>
              <span>已发奖金<span class="font-bold text-gray-900">{{ item.issued }}</span></span>
              <span>累计绩效奖金<span class="font-bold text-gray-900">{{ item.cumulative_merit_pay }}</span></span>
            </div>
             <el-link type="primary" class="mt-2 ml-2" href="/admin/work/user/performance">查看更多</el-link>

          </div>
        </el-card>
        <!-- 消息通知卡片 -->
        <el-card class="!border-none flex-1" shadow="never">
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2 text-lg font-bold">
              <!-- 气泡对话框icon -->
              <span class="w-6 h-6 flex items-center justify-center bg-blue-100 rounded-full">
                 <image-contain
                     width="20px"
                     height="20px"
                     :src="svg.xxtz"
                     class="group-hover:scale-110 transition-transform duration-200"
                 />
              </span>
              <span>消息通知</span>
            </div>
            <el-link type="primary" href="/admin/message/system_msg">查看更多</el-link>
          </div>
          <div class="flex flex-col divide-y divide-gray-100">
            <div v-for="msg in messages" :key="msg.create_time" class="flex justify-between items-center text-[15px] py-2">
              <span class="truncate">{{ msg.content }}</span>
              <span class="text-gray-400 text-sm ml-4 whitespace-nowrap">{{ formatDate(msg.create_time) }}</span>
            </div>
          </div>
        </el-card>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { getUserWorkbench } from '@/api/app';
import { onMounted, ref } from 'vue';

// 类型声明
interface UserInfo {
  avatar?: string;
  username?: string;
  dept_name?: string;
  jobs_name?: string;
  job_no?: string;
  gender?: string;
  mobile?: string;
  id_type?: string;
  entry_date?: string;
  education?: string;
  email?: string;
  id_number?: string;
  status?: string;
  age?: number;
  address?: string;
  salary?: string;
}
interface Notice { title?: string }
interface PerformanceItem {
  statistical_month?: string;
  work_score_desc?: string;
  merit_pay?: string;
  issued?: string;
  cumulative_merit_pay?: string;
}
interface MessageItem {
  content?: string;
  create_time?: string;
}
interface SvgItem {
    grzx?: string;
    jxtj?: string;
    xxtz?: string;
    gsgg?: string;
}

// 响应式数据
const userinfo = ref<UserInfo>({});
const notice = ref<Notice>({});
const performance = ref<PerformanceItem[]>([]);
const messages = ref<MessageItem[]>([]);
const svg = ref<SvgItem[]>([]);

// 获取工作台数据
const getData = async () => {
  try {
    const res = await getUserWorkbench();
    userinfo.value = res.userinfo;
    notice.value = res.notice;
    performance.value = res.performance;
    messages.value = res.system_msg;
    svg.value = res.svg;
  } catch (err) {
    console.error('获取工作台数据失败:', err);
  }
};

onMounted(() => {
  getData();
});

// 格式化日期，只显示年月日
function formatDate(dateStr: string|undefined) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${y}-${m}-${day}`;
}
</script>

<style scoped>
.el-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px 0 rgba(0,0,0,0.03);
}
body, html, #app {
  margin: 0;
  padding: 0;
}
</style>
