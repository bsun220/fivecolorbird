<template>
    <div class="login flex flex-col">
        <div class="relative flex-1 flex items-center justify-center min-h-screen">
            <!-- 登录卡片容器（居中靠右） -->
            <div class="relative w-full max-w-md mr-[-20%]">
                <!-- 图片（卡片正上方居中） -->
                <div class="absolute -top-12 left-1/2 transform -translate-x-1/2 w-[176px] h-[49px] z-10">
                    <image-contain :src="config.login_image" width="100%" height="100%" />
                </div>

                <!-- 登录表单 -->
                <div class="login-form flex flex-col justify-center px-10 py-10 bg-white/10 backdrop-blur-sm rounded-lg">
                    <div class="text-center text-3xl font-medium mb-8 text-white">{{ config.web_name }}</div>

                    <el-form ref="formRef" :model="formData" size="large" :rules="rules">
                        <el-form-item prop="account">
                            <el-input
                                v-model="formData.account"
                                placeholder="请输入账号"
                                @keyup.enter="handleEnter"
                                class="custom-input"
                            >
                                <template #prepend>
                                    <div class="prepend-icon">
                                        <icon name="el-icon-User" size="16" />
                                    </div>
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
                                class="custom-input"
                            >
                                <template #prepend>
                                    <div class="prepend-icon">
                                        <icon name="el-icon-Lock" size="16" />
                                    </div>
                                </template>
                            </el-input>
                        </el-form-item>
                    </el-form>

                    <div class="mb-5">
                        <el-checkbox v-model="remAccount" label="记住账号" class="text-white"></el-checkbox>
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

<style scoped>
/* 全局输入框样式 */
.custom-input :deep(.el-input-group__prepend) {
    background: transparent !important;
    border: none !important;
    padding: 0 10px !important;
}

/* 图标容器样式 */
.prepend-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent !important;
}

/* 图标本身样式 */
.prepend-icon .icon {
    background: transparent !important;
    color: white !important;
    font-size: 16px;
}

/* 输入框整体样式 */
.custom-input :deep(.el-input__wrapper) {
    background-color: transparent !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    box-shadow: none !important;
}

/* 输入框文字样式 */
.custom-input :deep(.el-input__inner) {
    color: white !important;
}

.custom-input :deep(.el-input__inner::placeholder) {
    color: rgba(255, 255, 255, 0.6) !important;
}
</style>

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
    background-image: url('./images/login_bg2.png');
    @apply min-h-screen bg-no-repeat bg-center bg-cover;
    .login-card {
        height: 400px;
    }
}
</style>
