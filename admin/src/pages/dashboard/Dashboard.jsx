import { useEffect, useState } from "react";

import {
  getDashboardStats,
} from "../../services/dashboardService";

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
  Line,
  Bar,
} from "react-chartjs-2";

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

  const [stats, setStats] =
    useState(null);

  const [activityChart, setActivityChart] =
    useState([]);

  const [productChart, setProductChart] =
    useState([]);

  useEffect(() => {

    loadStats();

  }, []);

  const loadStats = async () => {

  try {

    const data =
      await getDashboardStats();

    setStats(data);

    setActivityChart(
      data?.activity_chart || []
    );

    setProductChart(
      data?.product_chart || []
    );

  } catch (error) {

    console.error(error);

    setStats({
      users: 0,
      categories: 0,
      products: 0,
      activity_logs: 0,
    });

    setActivityChart([]);

    setProductChart([]);

  }

};
  if (!stats) {

    return (
      <p>Loading...</p>
    );
  }

  const activityData = {

    labels:
  (activityChart || []).map(
    (item) => item.date
  ),

    datasets: [
      {
        label:
          "Aktivitas",

        data:
  (activityChart || []).map(
    (item) => item.total
  ),

        tension: 0.4,
      },
    ],
  };

  const productData = {

    labels:
  (productChart || []).map(
    (item) => item.name
  ),

    datasets: [
      {
        label:
          "Produk",

        data:
  (productChart || []).map(
    (item) => item.total
  ),
      },
    ],
  };

  return (
    <div>

      <h1 className="text-3xl font-bold mb-6">
        Dashboard
      </h1>

      <div className="grid grid-cols-4 gap-4">

        <div className="bg-white p-6 rounded shadow">
          <h3>Total Users</h3>
          <p className="text-3xl font-bold">
            {stats?.users ?? 0}
          </p>
        </div>

        <div className="bg-white p-6 rounded shadow">
          <h3>Total Categories</h3>
          <p className="text-3xl font-bold">
            {stats?.categories ?? 0}
          </p>
        </div>

        <div className="bg-white p-6 rounded shadow">
          <h3>Total Products</h3>
          <p className="text-3xl font-bold">
            {stats?.products ?? 0}

          </p>
        </div>

        <div className="bg-white p-6 rounded shadow">
          <h3>Activity Logs</h3>
          <p className="text-3xl font-bold">
            {stats?.activity_logs ?? 0}
          </p>
        </div>

      </div>

      <div className="grid grid-cols-2 gap-6 mt-6">

        <div className="bg-white p-5 rounded shadow">

          <h2 className="text-lg font-semibold mb-4">
            Aktivitas 7 Hari Terakhir
          </h2>

          <Line
            data={activityData}
          />

        </div>

        <div className="bg-white p-5 rounded shadow">

          <h2 className="text-lg font-semibold mb-4">
            Produk per Kategori
          </h2>

          <Bar
            data={productData}
          />

        </div>

      </div>

    </div>
  );
}