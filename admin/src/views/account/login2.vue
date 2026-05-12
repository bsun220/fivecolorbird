<template>
    <div class="login flex flex-col">

        <div class="flex-1 flex items-center justify-center relative">
            <!-- 背景图片，固定在左上角 -->
            <div class="absolute left-10 top-10 w-[120px] h-[80px] m-4">
                <image-contain :src="config.login_image" width="100%" height="100%" />
            </div>

            <!-- 登录卡片 -->
            <div class="login-card flex rounded-md overflow-hidden z-10">
                <div class="login-form flex flex-col justify-center px-10 py-10 md:w-[400px] w-[375px] flex-none mx-auto bg-transparent backdrop-blur-sm">
                    <div class="text-center text-3xl font-medium mb-8 text-white">{{ config.web_name }}</div>
                    <el-form ref="formRef" :model="formData" size="large" :rules="rules">
                        <el-form-item prop="account">
                            <el-input
                                v-model="formData.account"
                                placeholder="请输入账号"
                                @keyup.enter="handleEnter"
                            >
                                <template #prepend>
                                    <icon name="el-icon-User" size="16" />
                                </template>
                            </el-input>
                        </el-form-item>
                        <el-form-item prop="password">
                            <el-input
                                ref="passwordRef"
                                v-model="formData.password"
                                show-password
                                placeholder="请输入密码"
                                @keyup.enter="handleLogin"
                            >
                                <template #prepend>
                                    <icon name="el-icon-Lock" size="16" />
                                </template>
                            </el-input>
                        </el-form-item>
                    </el-form>
                    <div class="mb-5">
                        <el-checkbox v-model="remAccount" label="记住账号"></el-checkbox>
                    </div>
                    <el-button type="primary" size="large" :loading="isLock" @click="lockLogin">
                        登录
                    </el-button>
                </div>
            </div>
        </div>
        <layout-footer />
    </div>
</template>

<script lang="ts" setup>
import type { FormInstance, InputInstance } from 'element-plus'
import { computed, onMounted, reactive, ref, shallowRef } from 'vue'

import { ACCOUNT_KEY } from '@/enums/cacheEnums'
import { PageEnum } from '@/enums/pageEnum'
import { useLockFn } from '@/hooks/useLockFn'
import LayoutFooter from '@/layout/components/footer.vue'
import useAppStore from '@/stores/modules/app'
import useUserStore from '@/stores/modules/user'
import cache from '@/utils/cache'

const passwordRef = shallowRef<InputInstance>()
const formRef = shallowRef<FormInstance>()
const appStore = useAppStore()
const userStore = useUserStore()
const route = useRoute()
const router = useRouter()
const remAccount = ref(false)
const config = computed(() => appStore.config)
const formData = reactive({
    account: '',
    password: ''
})
const rules = {
    account: [
        {
            required: true,
            message: '请输入账号',
            trigger: ['blur']
        }
    ],
    password: [
        {
            required: true,
            message: '请输入密码',
            trigger: ['blur']
        }
    ]
}
// 回车按键监听
const handleEnter = () => {
    if (!formData.password) {
        return passwordRef.value?.focus()
    }
    handleLogin()
}
// 登录处理
const handleLogin = async () => {
    await formRef.value?.validate()
    // 记住账号，缓存
    cache.set(ACCOUNT_KEY, {
        remember: remAccount.value,
        account: remAccount.value ? formData.account : ''
    })
    await userStore.login(formData)
    const {
        query: { redirect }
    } = route
    const path = typeof redirect === 'string' ? redirect : PageEnum.INDEX
    router.push(path)
}
const { isLock, lockFn: lockLogin } = useLockFn(handleLogin)

onMounted(() => {
    const value = cache.get(ACCOUNT_KEY)
    if (value?.remember) {
        remAccount.value = value.remember
        formData.account = value.account
    }
})
</script>

<style lang="scss" scoped>
.login {
    background-image: url('./images/login_bg1.png');
    @apply min-h-screen bg-no-repeat bg-center bg-cover;
    .login-card {
        height: 400px;
    }
}
</style>
