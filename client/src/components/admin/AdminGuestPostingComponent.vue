<template>
    <div class="container">
        <div class="row justify-space-between align-center">
            <div class="left">
                <h1>Guest Posting and Niche edits list</h1>
                <p class="mt-2">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
            </div>
        </div>
        <div class="mb-4">
            <h5 class="mb-4">{{ total }} platforms found</h5>
        </div>
        <table class="admin-table" v-if="Object.keys(platforms).length">
            <thead>
            <tr>
                <td>
                    <VCheckbox
                        @click="selectAll"
                        hide-details
                        :value="selectedAll"
                    >
                    </VCheckbox>
                </td>
                <th @click="changeSortingAndDirection('id')">
                    <span :class="{ 'underline' : sorting === 'id' }">
                        ID
                    </span>
                </th>
                <th @click="changeSortingAndDirection('website_url')">
                    <span :class="{ 'underline' : sorting === 'website_url' }">
                        Website
                    </span>
                </th>
                <th
                    @click="changeSortingAndDirection('created_at')"
                    class="text-center">
                    <span :class="{ 'underline' : sorting === 'created_at' }">
                        Created At
                    </span>
                </th>
                <th
                    @click="changeSortingAndDirection('updated_at')"
                    class="text-center">
                    <span :class="{ 'underline' : sorting === 'updated_at' }">
                        Updated At
                    </span>
                </th>
                <th class="text-right">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="platform in platforms" :key="platform.id">
                <td>
                    <VCheckbox
                        :value="!!chosen[platform.id]"
                        @click="selectPlatform(platform.id)"
                        hide-details
                    ></VCheckbox>
                </td>
                <td>
                    {{ platform.id }}
                </td>
                <td>
                    <div class="link">
                        <span class="website-link">
                            {{ platform.websiteUrl }}
                        </span>
                    </div>
                    <div class="topics">
                        <VChip x-small
                               class="pa-0 px-1 mr-1 mb-1 chip"
                               v-for="(topic, id) in platform.topics"
                               :key="id"
                        >
                            {{ topic.name }}
                        </VChip>
                    </div>
                </td>
                <td class="text-center">
                    {{ platform.createdAt | formatDataFilter }}
                </td>
                <td class="text-center">
                    <span v-if="platform.updatedAt === platform.createdAt">
                        N/A
                    </span>
                    <span v-else>
                        {{ platform.updatedAt | formatDataFilter }}
                    </span>
                </td>
                <td class="text-right">
                    <VBtn
                        fab
                        x-small
                        color="purple"
                        dark
                        class="mr-3"
                        @click="onViewPlatform(platform)"
                    >
                        <VIcon>mdi-eye</VIcon>
                    </VBtn>
                    <VBtn
                        fab
                        x-small
                        color="green"
                        dark
                        class="mr-3"
                        @click="onEditPlatform(platform.id)"
                    >
                        <VIcon>mdi-pencil</VIcon>
                    </VBtn>
                    <VBtn fab
                          x-small
                          color="red"
                          dark
                          @click="onDeletePlatformDialog(platform)"
                    >
                        <VIcon>mdi-delete</VIcon>
                    </VBtn>
                </td>
            </tr>
            </tbody>
        </table>
        <h1 v-else class="text-center">Platforms weren't found:( ...</h1>

        <VRow class="justify-space-between mt-4" v-if="Object.keys(platforms).length">
            <ul class="pagination">
                <li>
                    <VBtn :disabled="page === 1" @click="page -= 1" fab small>
                        <VIcon>mdi-chevron-left</VIcon>
                    </VBtn>
                </li>
                <li v-for="firstPage in firstPages" :key="firstPage">
                    <VBtn
                        small
                        fab
                        @click="onChangePage(firstPage)"
                        :color="firstPage === page ? 'blue' : 'white'">{{ firstPage }}</VBtn>
                </li>
                <li v-if="pages.length > 4">
                    <VBtn small fab>...</VBtn>
                </li>
                <li v-if="pages.length > 4">
                    <ul class="pl-0">
                        <li v-for="lastPage in lastPages" :key="lastPage">
                            <VBtn
                                small
                                fab
                                @click="onChangePage(lastPage)"
                                :color="lastPage === page ? 'blue' : 'white'"
                            >{{ lastPage }}</VBtn>
                        </li>

                    </ul>
                </li>
                <li>
                    <VBtn
                        :disabled="page === lastPage"
                        @click="page += 1"
                        fab
                        small
                    >
                        <VIcon>mdi-chevron-right</VIcon>
                    </VBtn>
                </li>
            </ul>
            <VCol cols="2">
                <VSelect
                    solo
                    dense
                    :items="[5, 10, 15, 20, 25, 30]"
                    label="Per page"
                    @change="onSelectPerPage"
                    v-model="perPage"
                >
                </VSelect>
            </VCol>
        </VRow>

        <ActionButtons />

        <DeleteOnePlatformDialog
            :visible="deleteOnePlatformDialog"
            :platform="deletePlatformObj"
            @close="deleteOnePlatformDialog = false"
            @on-delete="onPlatformDeleted"
        />

        <AdminPlatformsFooter
            :chosen-platforms-ids="chosenPlatformsIds"
            @unselect-all="unSelectAll"
            @platforms-delete="onPlatformsDelete"
            @export="onExport"
            @updateByApi="unSelectAll"
        />

        <DeletePlatformsDialog
            :platforms="platformsByIds"
            :visibility="deletePlatformsDialog"
            @close="deletePlatformsDialog = false"
            @on-delete="onPlatformsDeleted"
        />

        <ViewPlatformDialog
            v-if="Object.keys(viewPlatform).length"
            :visibility="viewPlatformDialog"
            :platform="viewPlatform"
            @close-dialog="viewPlatformDialog = false"
        />
    </div>
</template>

<script>
import rolemixin from '@/mixins/rolemixin';
import filterMixin from '@/mixins/filterMixin';
import valueFormatMixin from '@/mixins/valueFormatMixin';
import DeleteOnePlatformDialog from '@/components/platform/DeleteOnePlatformDialog';
import ActionButtons from '@/components/platform/ActionButtons';
import guestPostingMixin from '@/mixins/guestPostingMixin';
import AdminPlatformsFooter from '@/components/platform/AdminPlatformsFooter';
import DeletePlatformsDialog from '@/components/platform/DeletePlatformsDialog';
import { mapActions } from 'vuex';
import * as actions from '@/store/modules/platforms/types/actions';
import ViewPlatformDialog from '@/components/admin/ViewPlatformDialog';

export default {
    name: 'AdminPostingComponent',
    components: {
        DeletePlatformsDialog,
        DeleteOnePlatformDialog,
        ActionButtons,
        AdminPlatformsFooter,
        ViewPlatformDialog
    },
    mixins: [rolemixin, filterMixin, valueFormatMixin, guestPostingMixin],
    data: () => ({
        deleteOnePlatformDialog: false,
        deletePlatformsDialog: false,
        deletePlatformObj: {},
        perPage: 10,
        viewPlatformDialog: false,
        viewPlatform: {}
    }),
    methods: {
        ...mapActions('platforms', {
            fetchPlatformsNotInTrash: actions.FETCH_PLATFORMS_NOT_IN_TRASH
        }),
        onViewPlatform(platform) {
            this.viewPlatform = platform;
            this.viewPlatformDialog = true;
        },
        onEditPlatform(id) {
            this.$router.push({ path: '/platforms/' + id });
        },
        onPlatformsDeleted() {
            this.unSelectAll();
            this.updatePlatformsOnPage();
        },
        onExport() {
            this.unSelectAll();
        },
        onPlatformDeleted() {
            this.unSelectAll();
            this.updatePlatformsOnPage();
        },
        onDeletePlatformDialog(platform) {
            this.deletePlatformObj = platform;
            this.deleteOnePlatformDialog = true;
        },
        onPlatformsDelete() {
            this.deletePlatformsDialog = true;
        },
        async updatePlatformsOnPage() {
            const response = await this.fetchPlatformsNotInTrashAction();
            this.currentPage = response.current_page;
            this.lastPage = response.last_page;
            this.total = response.total;
            this.reCalculatePages();
            this.pageBack();
        },
        pageBack() {
            if (!Object.keys(this.platforms).length && this.page > 1) this.page -= 1;
        },
        async changeSortingAndDirection(sorting) {
            this.sorting = sorting;
            this.direction = this.direction === 'desc' ? 'asc' : 'desc';
            await this.fetchPlatformsNotInTrashAction();
        },
        async fetchPlatformsNotInTrashAction() {
            return await this.fetchPlatformsNotInTrash({
                page: this.page,
                perPage: this.perPage,
                sorting: this.sorting,
                direction: this.direction,
            });
        }
    },
    async mounted() {
        const response = await this.fetchPlatformsNotInTrashAction();
        this.currentPage = response.current_page;
        this.lastPage = response.last_page;
        this.total = response.total;
        this.reCalculatePages();
        this.initializeChosenPlatformsState();
    },
    watch: {
        async page() {
            this.chosen = {};
            await this.fetchPlatformsNotInTrashAction();
            this.reCalculatePages();
            this.initializeChosenPlatformsState();
        },
        async perPage() {
            this.page = 1;
            const response = await this.fetchPlatformsNotInTrashAction();
            this.currentPage = response.current_page;
            this.lastPage = response.last_page;
            this.total = response.total;
            this.reCalculatePages();
            this.initializeChosenPlatformsState();
        },
        chosen() {
            this.selectedAll =
                Object.values(this.chosen).filter(value => value).length === this.platforms.length;
        }
    },
    computed: {
        platformsByIds() {
            return this.platforms
                .filter(
                    platform => this.chosenPlatformsIds.includes(
                        platform.id.toString()
                    )
                );
        }
    }
}
</script>

<style scoped>
/*@import "../../assets/styles/main.css";*/
@import "../../assets/styles/admin-table.css";
.float-btn-action {
    position: fixed;
    bottom: 25px;
    right: 25px;
}

.chip {
    font-size: 10px;
    font-weight: 400 !important;
}

.v-application--is-ltr .v-input--selection-controls__input {
    margin-right: 0;
}

.v-input--selection-controls {
    margin-top: 0;
    padding-top: 0;
}

.pagination {
    list-style: none;
}

.pagination li {
    cursor: pointer;
    display: inline-block;
    margin-right: 15px;
    transition: 0.5s;
}

.order-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: white;
    box-shadow:
        0px 2px 4px 4px rgba(0, 0, 0, 0.2),
        0px 4px 5px 5px rgba(0, 0, 0, 0.14),
        0px 1px 10px 4px rgba(0, 0, 0, 0.12);
    display: none;
}

.filters {
    border-radius: 10px;
    border: 2px solid #2f80ed;
    padding-left: 30px;
}

.not-uppercase {
    text-transform: none;
}
</style>