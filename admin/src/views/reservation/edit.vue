<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            :title="popupTitle"
            :async="true"
            width="550px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form ref="formRef" :model="formData" label-width="90px" :rules="formRules">
                <el-form-item label="用户" prop="user_id">
                    <el-input v-model="formData.user.account" disabled clearable placeholder="请输入用户" />
                </el-form-item>
                <el-form-item label="预约日期" prop="date">
                    <el-date-picker 
                        class="flex-1 !flex"
                        v-model="formData.date"
                        clearable
                        type="date"
                        value-format="YYYYMMDD"
                        disabled
                        placeholder="选择预约日期">
                    </el-date-picker>
                </el-form-item>
                
                <el-form-item label="预约时间段" prop="time_slot">
                    <el-input v-model="formData.time_slot" disabled clearable placeholder="请输入预约时间段" />
                </el-form-item>
                <el-form-item label="预约人" prop="name">
                    <el-input v-model="formData.name"  clearable placeholder="请输入预约人" />
                </el-form-item>
                <el-form-item label="手机号" prop="mobile">
                    <el-input v-model="formData.mobile"  clearable placeholder="请输入手机号" />
                </el-form-item>
                <el-form-item label="人数" prop="num">
                    <el-input v-model="formData.num" clearable placeholder="请输入人数" />
                </el-form-item>
                <el-form-item label="备注" prop="note">
                    <el-input v-model="formData.note" clearable placeholder="请输入备注" />
                </el-form-item>
                <el-form-item label="公司名称" prop="company">
                    <el-input v-model="formData.company" disabled clearable placeholder="请输入公司名称" />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>

<script lang="ts" setup name="reservationEdit">
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import { apiReservationAdd, apiReservationEdit, apiReservationDetail } from '@/api/reservation'
import { timeFormat } from '@/utils/util'
import type { PropType } from 'vue'
defineProps({
    dictData: {
        type: Object as PropType<Record<string, any[]>>,
        default: () => ({})
    }
})
const emit = defineEmits(['success', 'close'])
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const mode = ref('add')


// 弹窗标题
const popupTitle = computed(() => {
    return mode.value == 'edit' ? '编辑预约表' : '新增预约表'
})

// 表单数据
const formData = reactive({
    id: '',
    user_id: '',
    date: '',
    time_slot: '',
    name: '',
    mobile: '',
    num: '',
    note: '',
    company: '',
    user: {account:''}

})


// 表单验证
const formRules = reactive<any>({

})


// 获取详情
const setFormData = async (data: Record<any, any>) => {
    for (const key in formData) {
        if (data[key] != null && data[key] != undefined) {
            //@ts-ignore
            formData[key] = data[key]
        }
    }
    
    
}

const getDetail = async (row: Record<string, any>) => {
    const data = await apiReservationDetail({
        id: row.id
    })
    setFormData(data)
}


// 提交按钮
const handleSubmit = async () => {
    await formRef.value?.validate()
    const data = { ...formData,  }
    mode.value == 'edit' 
        ? await apiReservationEdit(data) 
        : await apiReservationAdd(data)
    popupRef.value?.close()
    emit('success')
}

//打开弹窗
const open = (type = 'add') => {
    mode.value = type
    popupRef.value?.open()
}

// 关闭回调
const handleClose = () => {
    emit('close')
}



defineExpose({
    open,
    setFormData,
    getDetail
})
</script>
