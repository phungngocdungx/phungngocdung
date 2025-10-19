const config = {
  title: "Phùng Ngọc Dũng | Full-Stack Developer",
  description: {
    long: "Explore the portfolio of Naresh, a full-stack developer and creative technologist specializing in interactive web experiences, 3D animations, and innovative projects. Discover my latest work, including Coding Ducks, The Booking Desk, Ghostchat, and more. Let's build something amazing together!",
    short:
      "Discover the portfolio of Naresh, a full-stack developer creating interactive web experiences and innovative projects.",
  },
  keywords: [
    "Naresh",
    "portfolio",
    "full-stack developer",
    "creative technologist",
    "web development",
    "3D animations",
    "interactive websites",
    "Coding Ducks",
    "The Booking Desk",
    "Ghostchat",
    "web design",
    "GSAP",
    "React",
    "Next.js",
    "Spline",
    "Framer Motion",
  ],
  author: "Phùng Ngọc Dũng",
  email: "phungdung2708@gmail.com",
  site: "https://ngocdung.id.vn",

  get ogImg() {
    return this.site + "/assets/seo/og-image.png";
  },
  social: {
    twitter: "#!",
    linkedin: "#!",
    instagram: "#!",
    facebook: "https://www.facebook.com/phungngocdung27",
    github: "https://github.com/phungngocdungx",
    tiktok: "https://www.tiktok.com/@pndung05",
    zalo: "https://zalo.me/0965336741",
  },
};
export { config };
