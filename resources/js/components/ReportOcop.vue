<template>
  <div class="container mt-2">
    <loading-page v-if="isLoading" :loadingtext="loadingtext"></loading-page>
    <div class="row">
    <div class="col-lg-4">
      <Multiselect v-model="faculty" id="faculty" :options="facultys" :allowEmpty="false" placeholder="เลือกคณะ/หน่วยงาน" label="facultyname" track-by="facultyid" :show-labels="false"></Multiselect>
    </div>
    <div class="col-lg-2" v-if="all_data.length > 0">
      <button class="btn btn-primary" @click="exportWord">Export Word</button>
    </div>
    </div>
    <h4>รายงานความเชื่อมโยงโครงการ Output Outcome และเป้าหมาย ตัวชี้วัดตามแผนพัฒนาการศึกษา ฉบับที่ 13</h4>
    <table class="table table-bordered" style="background-color: white;">
      <thead>
        <tr>
          <th>ที่</th>
          <th>รายการคำขอ</th>	
          <th>จำนวนเงิน</th>	
          <th>เป้าหมาย13</th>	
          <th>ตชว13</th>
          <th>output</th>	
          <th>kpi</th>
          <th>ค่าเป้าหมาย</th>	
          <th>outcome</th>
          <th>impact</th>
        </tr>
      </thead>
      <tbody>
        <template v-for="p in all_data">
          <tr :key="'tr1'+p.gbl1id">
            <td colspan="2">{{`${p.gbl1id}:${p.gbl1name}`}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <template v-for="pj in p.projectid">
            <tr :key="'tr2'+p.gbl1id+pj.projectid">
              <td colspan="3" class="pl-4">{{ `${pj.projectid}:${pj.projectworkname}`}}</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <template v-for="(l,k) in pj.listdata">
              <tr :key="'tr3'+p.gbl1id+pj.projectid+l.requestdetailid">
                <td class="pl-4">{{k+1}}</td>
                <td>{{l.detailname}}</td>
                <td class="text-right">{{Intl.NumberFormat('th-TH').format(l.amount)}}</td>
                <td>{{l.aim_name}}</td>
                <td>{{l.ind_name}}</td>
                <td>
                  <ul style="list-style: none;">
                    <li v-for="op in l.output" :key="'l1'+op.output_id+op.requestdetailid">{{op.output_tx}}</li>
                  </ul>
                </td>
                <td>
                  <ul style="list-style: none;">
                    <li v-for="op in l.output" :key="'l2'+op.output_id+op.requestdetailid">{{op.output_ind}}</li>
                  </ul>
                </td>
                <td>
                  <ul style="list-style: none;">
                    <li v-for="op in l.output" :key="'l3'+op.output_id+op.requestdetailid">{{op.output_goal}}</li>
                  </ul>
                </td>
                <td>
                  <ul style="list-style: none;">
                    <li v-for="oc in l.outcome" :key="'l4'+oc.outcome_id+oc.requestdetailid">{{oc.outcome_tx}}</li>
                  </ul>
                </td>
                <td>
                  <ul style="list-style: none;">
                    <li v-for="oc in l.outcome" :key="'l5'+oc.outcome_id+oc.requestdetailid">{{oc.outcome_impact}}</li>
                  </ul>
                </td>
              </tr>
            </template>
          </template>
        </template>
      </tbody>
    </table>
  </div>
</template>

<script>
  import LoadingPage from './LoadingPage.vue';
  import 'bootstrap/dist/css/bootstrap.css';
  import Multiselect from 'vue-multiselect'
  export default {
    name: 'report-ocop',
    props: ['bgyear', 'facultyid', 'statususe', 'loadingtext', 'urlexportword'],
    components: {
      LoadingPage,
      Multiselect
    },
  data () {
    return {
      isLoading: false,
      all_data: [],
      facultys: [],
      faculty: null
    }
  },
  computed: {
    facultyName () {
      return this.faculty ? this.facultys.filter(v=>v.facultyid==this.faculty.facultyid)[0].facultyname : (this.facultys.length > 0 ? this.facultys.filter(v=>v.facultyid==this.facultyid)[0].facultyname : null)
    }
  },
  methods: {
    getData () {
      const data = {
        "budgetyear": this.bgyear,
        "facultyid": this.faculty ? this.faculty.facultyid : this.facultyid,
      }
      console.log("data",data);
      this.isLoading = true
      axios.get("/bip/exportword/api/getalldata",{ params: data})
      .then(datax => {
          const alldata = datax.data
          console.log("savedata",alldata)
          this.all_data = this.groupByPlan(alldata)
          this.all_data.forEach((p,i) => {
            const projs = this.groupByProj(alldata,p).sort((ax, bx) => (ax.projectid+"").localeCompare(bx.projectid+""))
            projs.forEach((pj,j) => {
              projs[j].listdata = alldata.filter(v => v.gbl1id*1 === p.gbl1id*1 && v.projectid*1 === pj.projectid*1)
            });
            this.all_data[i].projectid = projs
          })
          this.isLoading = false
      })
    },
    groupByPlan(v) {
      const result = Object.values(v.reduce((acc, cur) => {
        const key = cur.gbl1id + cur.gbl1name
        acc[key] = acc[key] || {
          gbl1id: cur.gbl1id,
          gbl1name: cur.gbl1name,
          amount: 0,
          projectid: []
        };
        acc[key].amount += cur.amount*1;
        return acc;
      }, {}));
      return result
    },
    groupByProj(v,plan) {
      const result = Object.values((v.filter(v => v.gbl1id*1 === plan.gbl1id*1)).reduce((acc, cur) => {
        const key = cur.projectid + cur.projectworkname
        acc[key] = acc[key] || {
          projectid: cur.projectid,
          projectworkname: cur.projectworkname,
          amount: 0,
          listdata: []
        };
        acc[key].amount += cur.amount*1;
        return acc;
      }, {}));
      return result
    },
    sumBudget (res,expid,stg,proj) {
      const result = Object.values(res.filter(v => v.EXPENSESID==expid&&v.STRATEGICCODE*1===stg.STRATEGICCODE*1&&v.PROJECTWORKID*1===proj.PROJECTWORKID*1).reduce((acc, cur) => {
        const key = cur.EXPENSESID
        acc[key] = acc[key] || {
          EXPENSESID: cur.EXPENSESID,
          AMOUNT: 0,
        };
        acc[key].AMOUNT += cur.AMOUNT*1;
        return acc;
      }, {}));
      return result.length > 0 ? result[0].AMOUNT*1 : 0
    },
    getfac () {
      axios.get('/bip/exportword/api/getfacall',{
        params: {
          statususe: this.statususe,
          facultyid: this.facultyid
        }
      }).then((res)=>{
        const facs = res.data
        this.facultys = facs
        const fac_f = facs.filter(item=>item.facultyid==this.facultyid)
        this.faculty = fac_f.length > 0 ? fac_f[0] : null
      })
    },
    exportWord () {
      const data = {
        "facultyid": this.facultyid,
        "facultyname": this.facultyName,
        "all_data": this.all_data,
      }
      axios.post(this.urlexportword,data, { responseType: 'blob' })
      .then(response => {
        console.log(response);
        const blob = new Blob([response.data], { type: 'application/msword' });
        const downloadLink = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = downloadLink;
        a.download = 'รายงานความเชื่อมโยงค่าเป้าหมายและKPI.docx';
        a.click();
      })
      .catch(error => {
        // Handle the error
        console.error(error);
      });
    }
  },
  watch: {
    all_data (v) {
      console.log(v);
    },
    faculty (v) {
      console.log(v);
      this.getData()
    }
  },
  mounted () {
    this.getfac()
  }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss" scoped>

</style>