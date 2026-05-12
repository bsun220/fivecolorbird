<template>
    <div>
        <el-card class="!border-none mb-4" shadow="never">
            <el-form
                class="mb-[-16px]"
                :model="queryParams"
                inline
            >
                <el-form-item label="操作人" prop="admin_name">
                    <el-input class="w-[280px]" v-model="queryParams.admin_name" clearable placeholder="请输入操作人" />
                </el-form-item>
                <el-form-item label="操作板块" prop="title">
                    <el-input class="w-[280px]" v-model="queryParams.title" clearable placeholder="请输入操作板块" />
                </el-form-item>
                <el-form-item label="操作" prop="title">
                    <el-input class="w-[280px]" v-model="queryParams.action" clearable placeholder="请输入操作" />
                </el-form-item>
                <el-form-item label="ip地址" prop="ip">
                    <el-input class="w-[280px]" v-model="queryParams.ip" clearable placeholder="请输入ip地址" />
                </el-form-item>
                <el-form-item label="创建时间" prop="create_time">
                    <daterange-picker
                        type="daterange"
                        v-model:startTime="queryParams.start_time"
                        v-model:endTime="queryParams.end_time"
                    />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none" v-loading="pager.loading" shadow="never">
            <div class="mt-4">
                <el-table :data="pager.lists">
                    <el-table-column label="创建时间" prop="create_time" show-overflow-tooltip />
                    <el-table-column label="操作人" prop="admin_name" show-overflow-tooltip />
                    <el-table-column label="操作板块" prop="title" show-overflow-tooltip />
                    <el-table-column label="操作" prop="action" show-overflow-tooltip />
                    <el-table-column label="操作内容" prop="name" show-overflow-tooltip />
                    <el-table-column label="ip地址" prop="ip" show-overflow-tooltip />
                </el-table>
            </div>
            <div class="flex mt-4 justify-end">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
</template>

<script lang="ts" setup name="logLists">
import { usePaging } from '@/hooks/usePaging'
import { useDictData } from '@/hooks/useDictOptions'
import { apiLogLists, apiLogDelete } from '@/api/log'

// 是否显示编辑框
const showEdit = ref(false)


// 查询条件
const queryParams = reactive({
    admin_id: '',
    admin_name: '',
    title: '',
    create_time: '',
    ip: '',
    start_time: '',
    end_time: ''

})




// 获取字典数据
const { dictData } = useDictData('')

// 分页相关
const { pager, getLists, resetParams, resetPage } = usePaging({
    fetchFun: apiLogLists,
    params: queryParams
})

getLists()
</script>

