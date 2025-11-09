export default function Footer() {
  return (
    <footer className="bg-[#2b0d49] text-white">
      <div className="max-w-6xl mx-auto px-6 py-8 grid gap-6 lg:grid-cols-[auto_1fr_auto] items-start">
        <div className="flex flex-col gap-3">
          <div className="flex items-center">
            <img
              src="/assets/trulogo_horz-white.png"
              alt="Truist logo"
              className="h-8 w-auto"
            />
          </div>
          <p className="text-xs text-white/70 max-w-xs">
            Tailored banking experiences, demonstrated for students and simulation exercises only.
          </p>
        </div>

        <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 text-sm">
          {[
            ['Privacy', 'Accessibility'],
            ['Fraud & security', 'Limit use of my sensitive personal information'],
            ['Terms and conditions', 'Disclosures'],
          ].map((group, index) => (
            <ul key={index} className="space-y-2">
              {group.map((item) => (
                <li key={item}>
                  <a className="text-white/80 hover:text-white" href="#">
                    {item}
                  </a>
                </li>
              ))}
            </ul>
          ))}
        </div>
      </div>
      <div className="bg-black/90">
        <p className="max-w-6xl mx-auto px-6 py-3 text-center text-xs text-white/70">
          2025, Truist. All rights reserved.
        </p>
      </div>
    </footer>
  )
}