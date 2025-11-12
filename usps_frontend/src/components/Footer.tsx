import React from "react";

const Footer: React.FC = () => {
  return (
    <footer className="w-full text-[#1a2252] text-[0.75rem] py-10 mt-16">
      <div className="max-w-[1180px] mx-auto px-6">
        <div className="grid grid-cols-2 md:grid-cols-4 gap-y-8 gap-x-10">
          <div>
            <h4 className="text-sm font-semibold uppercase tracking-wide text-[#1a2252] mb-3">
              Helpful Links
            </h4>
            <ul className="space-y-1 text-[#4a4f6d]">
              <li><a href="#" className="hover:text-[#c5282c]">Contact Us</a></li>
              <li><a href="#" className="hover:text-[#c5282c]">Site Index</a></li>
              <li><a href="#" className="hover:text-[#c5282c]">FAQs</a></li>
              <li><a href="#" className="hover:text-[#c5282c]">Feedback</a></li>
            </ul>
          </div>
          <div>
            <h4 className="text-sm font-semibold uppercase tracking-wide text-[#1a2252] mb-3">
              On About.usps.com
            </h4>
            <ul className="space-y-1 text-[#4a4f6d]">
              <li><a href="#" className="hover:text-[#c5282c]">About USPS Home</a></li>
              <li><a href="#" className="hover:text-[#c5282c]">Newsroom</a></li>
              <li><a href="#" className="hover:text-[#c5282c]">USPS Service Updates</a></li>
              <li><a href="#" className="hover:text-[#c5282c]">Forms &amp; Publications</a></li>
              <li><a href="#" className="hover:text-[#c5282c]">Government Services</a></li>
              <li><a href="#" className="hover:text-[#c5282c]">Careers</a></li>
            </ul>
          </div>
          <div>
            <h4 className="text-sm font-semibold uppercase tracking-wide text-[#1a2252] mb-3">
              Other USPS Sites
            </h4>
            <ul className="space-y-1 text-[#4a4f6d]">
              <li><a href="#" className="hover:text-[#c5282c]">Business Customer Gateway</a></li>
              <li><a href="#" className="hover:text-[#c5282c]">Postal Inspectors</a></li>
              <li><a href="#" className="hover:text-[#c5282c]">Inspector General</a></li>
              <li><a href="#" className="hover:text-[#c5282c]">Postal Explorer</a></li>
              <li><a href="#" className="hover:text-[#c5282c]">National Postal Museum</a></li>
              <li><a href="#" className="hover:text-[#c5282c]">Resources for Developers</a></li>
            </ul>
          </div>
          <div>
            <h4 className="text-sm font-semibold uppercase tracking-wide text-[#1a2252] mb-3">
              Legal Information
            </h4>
            <ul className="space-y-1 text-[#4a4f6d]">
              <li><a href="#" className="hover:text-[#c5282c]">Privacy Policy</a></li>
              <li><a href="#" className="hover:text-[#c5282c]">Terms of Use</a></li>
              <li><a href="#" className="hover:text-[#c5282c]">FOIA</a></li>
              <li><a href="#" className="hover:text-[#c5282c]">No FEAR Act EEO Data</a></li>
            </ul>
          </div>
        </div>
        <div className="mt-10 text-center text-[#6b6f88] text-xs">
          &copy; {new Date().getFullYear()} USPS. All Rights Reserved.
        </div>
      </div>
    </footer>
  );
};

export default Footer;
