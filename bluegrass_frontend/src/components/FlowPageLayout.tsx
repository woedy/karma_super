import React from 'react';
import Footer from './Footer';

interface FlowPageLayoutProps {
  title: string;
  description?: string;
  eyebrow?: string;
  children: React.ReactNode;
  contentClassName?: string;
  afterContent?: React.ReactNode;
  secondaryContent?: React.ReactNode;
  disableDefaultSecondaryContent?: boolean;
}

const defaultSecondaryContent = (
  <div className="flex flex-col items-center gap-4 text-sm text-white/90 md:flex-row md:gap-6">
    <a href="#" className="font-medium hover:underline">
      Become a Member
    </a>
    <span className="hidden h-4 w-px bg-white/60 md:block" aria-hidden="true" />
    <a href="#" className="font-medium hover:underline">
      PIB
    </a>
  </div>
);

const FlowPageLayout: React.FC<FlowPageLayoutProps> = ({
  title,
  description,
  eyebrow,
  children,
  contentClassName = '',
  afterContent,
  secondaryContent,
  disableDefaultSecondaryContent = false,
}) => {
  const linksBlock = disableDefaultSecondaryContent
    ? secondaryContent ?? null
    : secondaryContent ?? defaultSecondaryContent;

  return (
    <div className="min-h-screen bg-[#4A9619] text-[#123524] flex flex-col">
      <main className="flex-1 flex items-center justify-center px-4 py-12">
        <div className="w-full max-w-3xl flex flex-col items-center gap-8">
          <div className="w-full rounded-[30px] bg-white shadow-[0_30px_60px_rgba(0,0,0,0.18)] px-10 py-12 text-center">
            <img
              src="/assets/blue_logo.png"
              alt="Bluegrass Community FCU"
              className="mx-auto h-12 w-auto"
            />

            <div className="mt-6 space-y-3">
              {eyebrow && (
                <p className="text-[0.65rem] font-semibold uppercase tracking-[0.35em] text-[#4A9619]">
                  {eyebrow}
                </p>
              )}
              <h1 className="text-2xl font-semibold text-gray-900">{title}</h1>
              {description && <p className="text-sm text-gray-600 leading-relaxed">{description}</p>}
            </div>

            <div className={`mt-10 text-left text-gray-800 ${contentClassName}`}>
              {children}
            </div>

            {afterContent && <div className="mt-8">{afterContent}</div>}
          </div>

          {linksBlock}
        </div>
      </main>

      <Footer />
    </div>
  );
};

export default FlowPageLayout;
