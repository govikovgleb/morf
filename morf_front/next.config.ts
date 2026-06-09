import type { NextConfig } from "next";

const apiHost = process.env.API_HOST || "web";
const apiPort = process.env.API_PORT || "80";
const apiUrl = `http://${apiHost}:${apiPort}`;

const nextConfig: NextConfig = {
  output: "standalone",
  images: {
    remotePatterns: [
      {
        protocol: "http",
        hostname: apiHost,
        port: apiPort,
      },
    ],
  },
  async rewrites() {
    return [
      {
        source: "/api/:path*",
        destination: `${apiUrl}/api/:path*`,
      },
      {
        source: "/storage/:path*",
        destination: `${apiUrl}/storage/:path*`,
      },
    ];
  },
};

export default nextConfig;
