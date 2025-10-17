import React from "react";

const Footer = () => {
  return (
    <footer className="bg-white py-6 text-center text-sm text-gray-700 mt-8 border-t border-gray-200">
      <div className="flex flex-col md:flex-row items-center justify-center gap-3 mb-3">
        <a href="#" className="text-purple-800 hover:underline">Contact Us</a>
        <a href="#" className="text-purple-800 hover:underline">Locations</a>
        <a href="#" className="text-purple-800 hover:underline">Disclosures</a>
        <a href="#" className="text-purple-800 hover:underline">Privacy Policy</a>
        <a href="#" className="text-purple-800 hover:underline">Open a Membership</a>
      </div>
      <p className="text-xs text-gray-600">Routing # 296076301</p>
      <p className="text-xs mt-2 text-gray-600">
        Affinity Plus Federal Credit Union is federally insured by the National Credit Union Administration. Copyright © 2025 Affinity Plus Federal Credit Union.
      </p>

      <div className="flex items-center justify-center gap-4 mt-4">
        <img src="/assets/equal-housing.png" alt="Equal Housing Lender" className="h-8 w-auto" />
        <img src="/assets/ncua.png" alt="NCUA Insured" className="h-8 w-auto" />
      </div>
    </footer>
  );
};

export default Footer;
