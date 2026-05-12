<template>
    <div>
<!--        <el-card class="!border-none mb-4" shadow="never">-->
<!--            <el-form-->
<!--                class="mb-[-16px]"-->
<!--                :model="queryParams"-->
<!--                inline-->
<!--            >-->
<!--                <el-form-item label="文件名" prop="node">-->
<!--                    <el-input class="w-[280px]" v-model="queryParams.name" clearable placeholder="请输入文件名" />-->
<!--                </el-form-item>-->

<!--                <el-form-item>-->
<!--                    <el-button type="primary" @click="resetPage">查询</el-button>-->
<!--                    <el-button @click="resetParams">重置</el-button>-->
<!--                </el-form-item>-->
<!--            </el-form>-->
<!--        </el-card>-->
        <el-card class="!border-none" v-loading="pager.loading" shadow="never">
            <div class="mt-4">
                <el-table :data="pager.lists">
                    <el-table-column label="文件名" prop="name" show-overflow-tooltip />
                    <el-table-column label="操作" width="120" fixed="right">
                        <template #default="{ row }">
                            <el-button
                                type="primary"
                                link
                                @click="handleDownload(row.url, row.name)"
                            >
                                下载
                            </el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="flex mt-4 justify-end">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
</template>

<script lang="ts" setup name="workFileLists">
import { usePaging } from '@/hooks/usePaging'
import { apiWorkFileLists } from '@/api/work_file'


// 查询条件
const queryParams = reactive({

})

// 分页相关
const { pager, getLists, resetParams, resetPage } = usePaging({
    fetchFun: apiWorkFileLists,
    params: queryParams
})

const handleDownload = (file_url: any, file_name: any) => {
    const link = document.createElement('a');
    link.href = file_url;
    link.download = file_name;
    link.click();
}

getLists()
</script>

