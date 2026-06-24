import {
  useEffect,
  useState,
} from "react";

import {
  Line,
  Bar,
} from "react-chartjs-2";

import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
} from "chart.js";

import {
  Users,
  FolderTree,
  Package,
  Activity,
  TrendingUp,
  UserPlus,
  PackagePlus,
  Clock,
  ShoppingBag,
  CreditCard,
  UserCircle2,
} from "lucide-react";

import {
  getDashboardStats,
} from "../../services/dashboardService";

import {
  useAuth,
} from "../../context/AuthContext";

import EventBadge from "../../components/common/EventBadge";

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend
);

export default function Dashboard() {

  const { user } = useAuth();

  const [loading, setLoading] =
    useState(true);

  const [stats, setStats] =
    useState(null);

  const [growth, setGrowth] =
    useState({});

  const [activityChart, setActivityChart] =
    useState([]);

  const [userChart, setUserChart] =
    useState([]);

  const [productChart, setProductChart] =
    useState([]);

  const [recentActivities, setRecentActivities] =
    useState([]);

  useEffect(() => {

    loadDashboard();

  }, []);

  const loadDashboard = async () => {

    try {

      const data =
        await getDashboardStats();

      console.log(data);

      setStats(
        data.statistics || {}
      );

      setGrowth(
        data.growth || {}
      );

      setActivityChart(
        data?.charts?.activity_chart || []
      );

      setUserChart(
        data?.charts?.user_chart || []
      );

      setProductChart(
        data?.charts?.products_by_category_chart || []
      );

      setRecentActivities(
        data?.recent_activities || []
      );

    } catch (error) {

      console.error(error);

      setStats({

        users: 0,

        customers: 0,

        categories: 0,

        products: 0,

        orders: 0,

        payments: 0,

        activity_logs: 0,

      });

      setGrowth({});

      setActivityChart([]);

      setUserChart([]);

      setProductChart([]);

      setRecentActivities([]);

    } finally {

      setLoading(false);

    }

  };

  /*
  |--------------------------------------------------------------------------
  | Loading Skeleton
  |--------------------------------------------------------------------------
  */

  if (loading) {

    return (

      <div className="space-y-6 animate-pulse">

        <div className="h-12 bg-slate-200 rounded-2xl w-72" />

        <div
          className="
            grid
            grid-cols-1
            md:grid-cols-2
            xl:grid-cols-4
            gap-6
          "
        >

          {[...Array(7)].map(
            (_, index) => (

              <div
                key={index}
                className="
                  bg-white
                  rounded-3xl
                  p-6
                  shadow-sm
                "
              >

                <div className="h-4 bg-slate-200 rounded w-24 mb-4" />

                <div className="h-10 bg-slate-200 rounded w-20 mb-4" />

                <div className="h-3 bg-slate-200 rounded w-32" />

              </div>

            )
          )}

        </div>

        <div
          className="
            grid
            grid-cols-1
            xl:grid-cols-2
            gap-6
          "
        >

          <div className="bg-white rounded-3xl h-96" />

          <div className="bg-white rounded-3xl h-96" />

        </div>

      </div>

    );

  }

  /*
  |--------------------------------------------------------------------------
  | Activity Chart
  |--------------------------------------------------------------------------
  */

  const activityData = {

    labels:
      activityChart.map(
        (item) => item.date
      ),

    datasets: [
      {

        label:
          "Aktivitas",

        data:
          activityChart.map(
            (item) => item.total
          ),

        borderColor:
          "#dc2626",

        backgroundColor:
          "rgba(220,38,38,0.15)",

        fill: true,

        tension: 0.4,

        pointRadius: 5,

        pointHoverRadius: 7,

      },
    ],

  };

  /*
  |--------------------------------------------------------------------------
  | User Registration Chart
  |--------------------------------------------------------------------------
  */

  const userData = {

    labels:
      userChart.map(
        (item) => item.date
      ),

    datasets: [
      {

        label:
          "Registrasi User",

        data:
          userChart.map(
            (item) => item.total
          ),

        borderColor:
          "#2563eb",

        backgroundColor:
          "rgba(37,99,235,0.15)",

        fill: true,

        tension: 0.4,

        pointRadius: 5,

        pointHoverRadius: 7,

      },
    ],

  };

  /*
  |--------------------------------------------------------------------------
  | Product Chart
  |--------------------------------------------------------------------------
  */

  const productData = {

    labels:
      productChart.map(
        (item) => item.name
      ),

    datasets: [
      {

        label:
          "Produk",

        data:
          productChart.map(
            (item) => item.total
          ),

        backgroundColor: [

          "#dc2626",

          "#ef4444",

          "#f97316",

          "#fb7185",

          "#f59e0b",

          "#ea580c",

        ],

        borderRadius: 12,

      },
    ],

  };

  /*
  |--------------------------------------------------------------------------
  | Chart Options
  |--------------------------------------------------------------------------
  */

  const chartOptions = {

    responsive: true,

    maintainAspectRatio: false,

    plugins: {

      legend: {

        position: "top",

      },

    },

  };
  return (

    <div className="space-y-8">

      {/* Welcome Section */}
      <div
        className="
          relative
          overflow-hidden
          rounded-[32px]
          bg-gradient-to-r
          from-red-600
          via-red-500
          to-orange-500
          p-8
          text-white
          shadow-xl
        "
      >

        <div className="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-white/10" />

        <div className="absolute -bottom-20 -left-20 w-72 h-72 rounded-full bg-white/10" />

        <div className="relative z-10">

          <p className="text-red-100 text-sm mb-2">
            Welcome back,
          </p>

          <h1 className="text-4xl font-bold">
            {user?.name || "Administrator"} 👋
          </h1>

          <p className="mt-3 text-red-100 max-w-2xl">
            Pantau performa bisnis,
            aktivitas sistem,
            dan pertumbuhan Kellogg's
            melalui dashboard enterprise
            yang modern dan real-time.
          </p>

        </div>

      </div>

      {/* Statistics */}
      <div
        className="
          grid
          grid-cols-1
          sm:grid-cols-2
          xl:grid-cols-4
          gap-6
        "
      >

        {[
          {
            title: "Total Users",
            value: stats?.users ?? 0,
            icon: Users,
            iconBg: "bg-blue-100",
            iconColor: "text-blue-600",
          },

          {
            title: "Customers",
            value: stats?.customers ?? 0,
            icon: UserCircle2,
            iconBg: "bg-indigo-100",
            iconColor: "text-indigo-600",
          },

          {
            title: "Categories",
            value: stats?.categories ?? 0,
            icon: FolderTree,
            iconBg: "bg-amber-100",
            iconColor: "text-amber-600",
          },

          {
            title: "Products",
            value: stats?.products ?? 0,
            icon: Package,
            iconBg: "bg-emerald-100",
            iconColor: "text-emerald-600",
          },

          {
            title: "Orders",
            value: stats?.orders ?? 0,
            icon: ShoppingBag,
            iconBg: "bg-purple-100",
            iconColor: "text-purple-600",
          },

          {
            title: "Payments",
            value: stats?.payments ?? 0,
            icon: CreditCard,
            iconBg: "bg-cyan-100",
            iconColor: "text-cyan-600",
          },

          {
            title: "Activity Logs",
            value: stats?.activity_logs ?? 0,
            icon: Activity,
            iconBg: "bg-red-100",
            iconColor: "text-red-600",
          },

        ].map((item) => {

          const Icon =
            item.icon;

          return (

            <div
              key={item.title}
              className="
                bg-white
                rounded-3xl
                p-6
                shadow-sm
                border
                border-slate-100
                hover:shadow-lg
                transition
              "
            >

              <div className="flex items-center justify-between">

                <div>

                  <p className="text-slate-500 text-sm">
                    {item.title}
                  </p>

                  <h3 className="text-4xl font-bold mt-2">
                    {item.value}
                  </h3>

                </div>

                <div
                  className={`
                    w-14
                    h-14
                    rounded-2xl
                    flex
                    items-center
                    justify-center
                    ${item.iconBg}
                  `}
                >

                  <Icon
                    className={`w-7 h-7 ${item.iconColor}`}
                  />

                </div>

              </div>

            </div>

          );

        })}

      </div>

      {/* Growth Cards */}
            <div
        className="
          grid
          grid-cols-1
          md:grid-cols-3
          gap-6
        "
      >

        {[
          {
            title: "New Users Today",
            value: growth?.new_users_today ?? 0,
            icon: UserPlus,
            iconBg: "bg-indigo-100",
            iconColor: "text-indigo-600",
          },

          {
            title: "New Products Today",
            value: growth?.new_products_today ?? 0,
            icon: PackagePlus,
            iconBg: "bg-emerald-100",
            iconColor: "text-emerald-600",
          },

          {
            title: "Activities Today",
            value: growth?.new_activities_today ?? 0,
            icon: TrendingUp,
            iconBg: "bg-red-100",
            iconColor: "text-red-600",
          },

        ].map((item) => {

          const Icon = item.icon;

          return (

            <div
              key={item.title}
              className="
                bg-white
                rounded-3xl
                p-6
                shadow-sm
                border
                border-slate-100
              "
            >

              <div className="flex items-center gap-4">

                <div
                  className={`
                    w-12
                    h-12
                    rounded-2xl
                    flex
                    items-center
                    justify-center
                    ${item.iconBg}
                  `}
                >

                  <Icon
                    className={`w-6 h-6 ${item.iconColor}`}
                  />

                </div>

                <div>

                  <p className="text-slate-500 text-sm">
                    {item.title}
                  </p>

                  <h3 className="text-3xl font-bold">
                    {item.value}
                  </h3>

                </div>

              </div>

            </div>

          );

        })}

      </div>

      {/* Charts */}
      <div
        className="
          grid
          grid-cols-1
          xl:grid-cols-2
          gap-6
        "
      >

        {/* Activity Chart */}
        <div
          className="
            bg-white
            rounded-3xl
            p-6
            shadow-sm
            border
            border-slate-100
          "
        >

          <div className="flex items-center justify-between mb-6">

            <div>

              <h2 className="text-xl font-bold text-slate-900">
                Activity Overview
              </h2>

              <p className="text-sm text-slate-500 mt-1">
                Aktivitas sistem dalam 7 hari terakhir
              </p>

            </div>

            <div
              className="
                w-12
                h-12
                rounded-2xl
                bg-red-100
                flex
                items-center
                justify-center
              "
            >

              <TrendingUp className="w-6 h-6 text-red-600" />

            </div>

          </div>

          <div className="h-80">

            <Line
              data={activityData}
              options={chartOptions}
            />

          </div>

        </div>

        {/* User Registration Chart */}
        <div
          className="
            bg-white
            rounded-3xl
            p-6
            shadow-sm
            border
            border-slate-100
          "
        >

          <div className="flex items-center justify-between mb-6">

            <div>

              <h2 className="text-xl font-bold text-slate-900">
                User Registrations
              </h2>

              <p className="text-sm text-slate-500 mt-1">
                Registrasi user dalam 7 hari terakhir
              </p>

            </div>

            <div
              className="
                w-12
                h-12
                rounded-2xl
                bg-blue-100
                flex
                items-center
                justify-center
              "
            >

              <Users className="w-6 h-6 text-blue-600" />

            </div>

          </div>

          <div className="h-80">

            <Line
              data={userData}
              options={chartOptions}
            />

          </div>

        </div>

      </div>

      {/* Product Category Chart */}
      <div
        className="
          bg-white
          rounded-3xl
          p-6
          shadow-sm
          border
          border-slate-100
        "
      >

        <div className="flex items-center justify-between mb-6">

          <div>

            <h2 className="text-xl font-bold text-slate-900">
              Products by Category
            </h2>

            <p className="text-sm text-slate-500 mt-1">
              Distribusi produk berdasarkan kategori
            </p>

          </div>

          <div
            className="
              w-12
              h-12
              rounded-2xl
              bg-amber-100
              flex
              items-center
              justify-center
            "
          >

            <Package className="w-6 h-6 text-amber-600" />

          </div>

        </div>

        <div className="h-96">

          <Bar
            data={productData}
            options={chartOptions}
          />

        </div>

      </div>

      {/* Recent Activities */}
            <div
        className="
          bg-white
          rounded-3xl
          shadow-sm
          border
          border-slate-100
          overflow-hidden
        "
      >

        <div className="p-6 border-b border-slate-100">

          <div className="flex items-center justify-between">

            <div>

              <h2 className="text-xl font-bold text-slate-900">
                Recent Activities
              </h2>

              <p className="text-sm text-slate-500 mt-1">
                Aktivitas terbaru pada sistem
              </p>

            </div>

            <div
              className="
                w-12
                h-12
                rounded-2xl
                bg-slate-100
                flex
                items-center
                justify-center
              "
            >

              <Clock className="w-6 h-6 text-slate-600" />

            </div>

          </div>

        </div>

        <div className="divide-y divide-slate-100">

          {recentActivities.length > 0 ? (

            recentActivities.map(
              (activity) => (

                <div
                  key={activity.id}
                  className="
                    px-6
                    py-5
                    hover:bg-slate-50
                    transition
                  "
                >

                  <div
                    className="
                      flex
                      flex-col
                      lg:flex-row
                      lg:items-center
                      lg:justify-between
                      gap-4
                    "
                  >

                    <div className="space-y-2">

                      <EventBadge
                        event={activity.event}
                      />

                      <p className="text-slate-700">

                        {activity.description ||
                          activity.log_name ||
                          "Aktivitas sistem"}

                      </p>

                      <div
                        className="
                          flex
                          flex-wrap
                          gap-2
                          text-sm
                          text-slate-500
                        "
                      >

                        <span>
                          Oleh:
                        </span>

                        <span className="font-medium">

                          {activity.causer?.name ||
                            "System"}

                        </span>

                      </div>

                    </div>

                    <div
                      className="
                        text-sm
                        text-slate-400
                        shrink-0
                      "
                    >

                      {activity.created_at
                        ? new Date(
                            activity.created_at
                          ).toLocaleString(
                            "id-ID"
                          )
                        : "-"}

                    </div>

                  </div>

                </div>

              )
            )

          ) : (

            <div className="px-6 py-12 text-center">

              <Clock
                className="
                  w-12
                  h-12
                  mx-auto
                  text-slate-300
                  mb-4
                "
              />

              <p className="text-slate-500">
                Belum ada aktivitas terbaru.
              </p>

            </div>

          )}

        </div>

      </div>

    </div>

  );

}