import React from 'react';
import {Theme, ThemeProvider} from '@gravity-ui/uikit';

const DARK = 'dark';
const LIGHT = 'light';
const DEFAULT_THEME = LIGHT;

type ThemeContextType = {
    theme: Theme;
    setTheme: (theme: Theme) => void;
};

export const ThemeContext = React.createContext<ThemeContextType>({
    theme: DEFAULT_THEME,
    setTheme: () => {},
});

export const useThemeContext = () => React.useContext(ThemeContext);

export type WrapperProps = {
    children: React.ReactNode;
};

export const Wrapper: React.FC<WrapperProps> = ({children}) => {
    const [theme, setTheme] = React.useState<Theme>(() => {
        return (localStorage.getItem('theme') as Theme) || DEFAULT_THEME;
    });

    React.useEffect(() => {
        localStorage.setItem('theme', theme);
    }, [theme]);

    return (
        <ThemeContext.Provider value={{theme, setTheme}}>
            <ThemeProvider theme={theme}>
                {children}
            </ThemeProvider>
        </ThemeContext.Provider>
    );
}; 